<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // =====================================
    // CUSTOMER: TABLE SETUP PAGE
    // =====================================
    public function showTableSetup()
    {
        return view('table.setup');
    }

    // =====================================
    // CUSTOMER: SAVE TABLE NUMBER IN SESSION
    // =====================================
    public function storeTableSetup(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer|min:1',
        ]);

        session([
            'table_number' => $request->table_number
        ]);

        return redirect()->route('menu.index')
            ->with('success', 'Table number set successfully.');
    }

    // =====================================
    // CUSTOMER: PLACE ORDER
    // =====================================
    public function store(Request $request)
    {
        $tableNumber = session('table_number');

        if (!$tableNumber) {
            return redirect()->route('customer.home')
                ->with('error', 'Please set the table number first.');
        }

        $items = collect($request->items ?? [])->filter(function ($item) {
            return isset($item['qty']) && (int) $item['qty'] > 0;
        })->values()->toArray();

        $request->merge(['items' => $items]);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:food_items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $sessionCode = $this->getOrCreateSessionCode($tableNumber);

            $total = 0;

            foreach ($request->items as $item) {
                $food = FoodItem::lockForUpdate()->findOrFail($item['id']);
                $qty = (int) $item['qty'];

                if (!$food->is_available) {
                    throw new \Exception($food->name . ' is not available.');
                }

                if ($food->stock < $qty) {
                    throw new \Exception('Not enough stock for ' . $food->name . '.');
                }

                $extraPrice = 0;
                $selectedOption = $item['option'] ?? null;

                if (!empty($food->options) && $selectedOption) {
                    foreach ($food->options as $opt) {
                        if (is_array($opt) && ($opt['name'] ?? null) === $selectedOption) {
                            $extraPrice = (float) ($opt['price'] ?? 0);
                            break;
                        }
                    }
                }

                $finalPrice = $food->price + $extraPrice;
                $total += $finalPrice * $qty;
            }

            // ✅ ALWAYS NEW ORDER (batch)
            $order = Order::create([
                'user_id' => auth()->id(),
                'table_number' => $tableNumber,
                'session_code' => $sessionCode,
                'total_price' => $total,
                'status' => 'Pending',
                'billing_status' => 'Ordering',

                // ✅ FIX HERE
                'payment_amount' => 0,
                'change_amount' => 0,
                'payment_status' => 'Unpaid',

                'date' => now(),
            ]);

            foreach ($request->items as $item) {
                $food = FoodItem::lockForUpdate()->findOrFail($item['id']);
                $qty = (int) $item['qty'];
                $selectedOption = $item['option'] ?? null;

                $extraPrice = 0;

                if (!empty($food->options) && $selectedOption) {
                    foreach ($food->options as $opt) {
                        if (is_array($opt) && ($opt['name'] ?? null) === $selectedOption) {
                            $extraPrice = (float) ($opt['price'] ?? 0);
                            break;
                        }
                    }
                }

                $finalPrice = $food->price + $extraPrice;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'food_item_id' => $food->id,
                    'qty' => $qty,
                    'price' => $finalPrice,
                    'option' => $selectedOption,
                    'status' => 'Pending',
                ]);

                $food->decrement('stock', $qty);
            }

            DB::commit();

            return redirect()->route('orders.my')
                ->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // =====================================
    // CUSTOMER: VIEW CURRENT SESSION ORDERS
    // =====================================
    public function myOrders()
    {
        $tableNumber = session('table_number');

        if (!$tableNumber) {
            return redirect()->route('customer.home')
                ->with('error', 'Please set the table number first.');
        }

        $activeOrder = Order::where('table_number', $tableNumber)
            ->activeSession()
            ->latest('id')
            ->first();

        if (!$activeOrder) {
            $orders = collect();
            return view('orders.my-orders', compact('orders'));
        }

        $orders = Order::with(['details.food'])
            ->where('table_number', $tableNumber)
            ->where('session_code', $activeOrder->session_code)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($orders as $order) {
            $statuses = $order->details->pluck('status');

            if ($statuses->contains('Pending')) {
                $order->computed_status = 'Pending';
            } elseif ($statuses->contains('Preparing')) {
                $order->computed_status = 'Preparing';
            } else {
                $order->computed_status = 'Delivered';
            }
        }

        return view('orders.my-orders', compact('orders'));
    }

    // =====================================
    // ADMIN: VIEW ALL ORDERS
    // =====================================
    public function index()
    {
        $orders = Order::with(['user', 'details.food'])
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    // =====================================
    // KITCHEN: VIEW ALL ACTIVE ORDERS
    // =====================================
    public function kitchen()
    {
        $orders = Order::with(['details.food'])
            ->where('billing_status', 'Ordering')
            ->orderBy('id', 'asc')
            ->get();

        $pendingOrders = collect();
        $preparingOrders = collect();
        $readyTables = [];

        foreach ($orders as $order) {
            $statuses = $order->details->pluck('status');

            if ($statuses->contains('Pending')) {
                $order->computed_status = 'Pending';
                $pendingOrders->push($order);
            } elseif ($statuses->contains('Preparing')) {
                $order->computed_status = 'Preparing';
                $preparingOrders->push($order);
            } else {
                $order->computed_status = 'Delivered';

                $tableKey = $order->table_number . '|' . $order->session_code;

                if (!isset($readyTables[$tableKey])) {
                    $readyTables[$tableKey] = [
                        'table_number' => $order->table_number,
                        'session_code' => $order->session_code,
                        'items' => collect(),
                    ];
                }

                foreach ($order->details as $detail) {
                    $readyTables[$tableKey]['items']->push($detail);
                }
            }   
        }

        return view('orders.kitchen', compact('pendingOrders', 'preparingOrders', 'readyTables'));
    }

    // =====================================
    // KITCHEN: UPDATE ORDER STATUS
    // =====================================
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Preparing,Delivered',
        ]);

        $order = Order::with('details')->findOrFail($id);

        foreach ($order->details as $detail) {
            $detail->status = $request->status;
            $detail->save();
        }

        return back()->with('success', 'Order batch status updated.');
    }

    // =====================================
    // HELPER: GET CURRENT ACTIVE SESSION
    // OR CREATE A NEW ONE
    // =====================================
    private function getOrCreateSessionCode($tableNumber): string
    {
        $activeOrder = Order::where('table_number', $tableNumber)
            ->activeSession()
            ->latest('id')
            ->first();

        if ($activeOrder) {
            return $activeOrder->session_code;
        }

        $lastOrderForTable = Order::where('table_number', $tableNumber)
            ->latest('id')
            ->first();

        if (!$lastOrderForTable) {
            return 'T' . $tableNumber . '-001';
        }

        $lastSession = $lastOrderForTable->session_code;

        if (preg_match('/T\d+\-(\d+)/', $lastSession, $matches)) {
            $nextNumber = str_pad(((int) $matches[1]) + 1, 3, '0', STR_PAD_LEFT);
            return 'T' . $tableNumber . '-' . $nextNumber;
        }

        return 'T' . $tableNumber . '-001';
    }

    public function proceedToCounter()
    {
        $tableNumber = session('table_number');

        if (!$tableNumber) {
            return redirect()->route('table.setup')
                ->with('error', 'Please set the table number first.');
        }

        $activeOrder = Order::where('table_number', $tableNumber)
            ->where('billing_status', 'Ordering')
            ->latest('id')
            ->first();

        if (!$activeOrder) {
            return redirect()->back()
                ->with('error', 'No active ordering session found for this table.');
        }

        Order::where('table_number', $tableNumber)
            ->where('session_code', $activeOrder->session_code)
            ->where('billing_status', 'Ordering')
            ->update([
                'billing_status' => 'Requested'
            ]);

        return redirect()->route('orders.my')
            ->with('success', 'Your bill request has been sent. Please proceed to the counter.');
    }

    public function billing()
    {
        // Get only sessions that requested billing
        $sessions = Order::select('table_number', 'session_code', DB::raw('SUM(total_price) as total'))
            ->where('billing_status', 'Requested')
            ->groupBy('table_number', 'session_code')
            ->orderBy('table_number')
            ->get();

        return view('admin.billing.index', compact('sessions'));
    }

    public function billingShow($table, $session)
    {
        $orders = Order::with(['details.food'])
            ->where('table_number', $table)
            ->where('session_code', $session)
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('admin.billing')
                ->with('error', 'Session not found.');
        }

        $total = $orders->sum('total_price');

        return view('admin.billing.show', compact('orders', 'table', 'session', 'total'));
    }

    public function confirmPayment(Request $request, $table, $session)
    {
        $request->validate([
            'payment' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $orders = Order::where('table_number', $table)
                ->where('session_code', $session)
                ->where('billing_status', 'Requested')
                ->get();

            if ($orders->isEmpty()) {
                throw new \Exception('No requested billing session found.');
            }

            $total = $orders->sum('total_price');
            $payment = (float) $request->payment;
            $change = $payment - $total;

            if ($payment < $total) {
                throw new \Exception('Insufficient payment.');
            }

            foreach ($orders as $order) {
                $order->update([
                    'payment_amount' => $payment,
                    'change_amount' => $change,
                    'payment_status' => 'Paid',
                    'billing_status' => 'Paid',
                    'status' => 'Completed',
                ]);
            }

            DB::commit();

            return back()->with('success', 'Payment confirmed.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function adminDashboard()
    {
        $totalFoods = \App\Models\FoodItem::count();
        $availableFoods = \App\Models\FoodItem::where('is_available', true)->count();
        $outOfStockFoods = \App\Models\FoodItem::where('stock', '<=', 0)->count();

        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'Pending')->count();
        $preparingOrders = Order::where('status', 'Preparing')->count();
        $deliveredOrders = Order::where('status', 'Delivered')->count();

        $billingRequests = Order::where('billing_status', 'Requested')
            ->select('table_number', 'session_code')
            ->groupBy('table_number', 'session_code')
            ->get()
            ->count();

        $recentOrders = Order::with('details.food')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalFoods',
            'availableFoods',
            'outOfStockFoods',
            'totalOrders',
            'pendingOrders',
            'preparingOrders',
            'deliveredOrders',
            'billingRequests',
            'recentOrders'
        ));
    }

    public function kitchenData()
    {
        $orders = Order::with(['details.food'])
            ->where('billing_status', 'Ordering')
            ->orderBy('table_number')
            ->get()
            ->groupBy(function ($order) {
                return $order->table_number . '|' . $order->session_code;
            });

        $groupedTables = [
            'Pending' => [],
            'Preparing' => [],
            'Delivered' => [],
        ];

        foreach ($orders as $groupKey => $tableOrders) {
            [$tableNumber, $sessionCode] = explode('|', $groupKey);

            $statuses = $tableOrders->pluck('status')->toArray();

            if (in_array('Pending', $statuses)) {
                $column = 'Pending';
            } elseif (in_array('Preparing', $statuses)) {
                $column = 'Preparing';
            } else {
                $column = 'Delivered';
            }

            $groupedTables[$column][] = [
                'table_number' => $tableNumber,
                'session_code' => $sessionCode,
                'orders' => $tableOrders->values(),
            ];
        }

        return response()->json($groupedTables);
    }
}
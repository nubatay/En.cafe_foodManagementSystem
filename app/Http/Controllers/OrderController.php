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
    // TABLE START URL - Set table in session
    // =====================================
    public function startTableSession($id)
    {
        // Validate table ID is a positive integer
        if (!is_numeric($id) || (int)$id < 1) {
            return redirect('/')->with('error', 'Invalid table number.');
        }

        $tableNumber = (int)$id;

        // Save table number to session
        session(['table_number' => $tableNumber]);

        // If not authenticated, redirect to login
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('success', "Table {$tableNumber} assigned. Please log in.");
        }

        // If authenticated, redirect to customer home
        return redirect()->route('customer.home')
            ->with('success', "Table {$tableNumber} assigned successfully.");
    }

    // =====================================
    // TABLE RESET URL - Clear table from session
    // =====================================
    public function resetTableSession()
    {
        session()->forget('table_number');

        return redirect()->route('customer.home')
            ->with('success', 'Table session has been reset.');
    }

    // =====================================
    // CUSTOMER: PLACE ORDER
    // =====================================
    public function store(Request $request)
    {
        try {
            $tableNumber = session('table_number');

            if (!$tableNumber) {
                return redirect()->route('customer.home')
                    ->with('error', 'Please set the table number first.');
            }

            $requestedSession = Order::where('table_number', $tableNumber)
                ->where('billing_status', 'Requested')
                ->latest('id')
                ->first();

            if ($requestedSession) {
                return redirect()->route('orders.my')
                    ->with('error', 'You already proceeded to the counter. You cannot place another order.');
            }

            $items = collect($request->items ?? [])->filter(function ($item) {
                return isset($item['qty']) && (int) $item['qty'] > 0;
            })->values()->toArray();

            if (empty($items)) {
                throw new \Exception('Please select at least one item with quantity greater than 0.');
            }

            $request->merge(['items' => $items]);

            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|exists:food_items,id',
                'items.*.qty' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            $sessionCode = $this->getOrCreateSessionCode($tableNumber);
            $total = 0;
            $validatedItems = [];

            // Validate all items before creating order
            foreach ($request->items as $item) {
                $food = FoodItem::lockForUpdate()->find($item['id']);

                if (!$food) {
                    throw new \Exception('Food item not found.');
                }

                if (!$food->is_available) {
                    throw new \Exception($food->name . ' is currently unavailable.');
                }

                $qty = (int) $item['qty'];

                if ($qty <= 0) {
                    throw new \Exception($food->name . ' quantity must be greater than 0.');
                }

                if ($qty > $food->stock) {
                    throw new \Exception('Not enough stock for ' . $food->name . '. Available: ' . $food->stock . ', Requested: ' . $qty);
                }

                $extraPrice = 0;
                $selectedOption = $item['option'] ?? null;

                if (!empty($food->options)) {
                    if (empty($selectedOption)) {
                        throw new \Exception($food->name . ' requires selecting an option.');
                    }
                    $optionFound = false;
                    foreach ($food->options as $opt) {
                        if (is_array($opt) && ($opt['name'] ?? null) === $selectedOption) {
                            $extraPrice = (float) ($opt['price'] ?? 0);
                            $optionFound = true;
                            break;
                        }
                    }
                    if (!$optionFound) {
                        throw new \Exception($food->name . ' option "' . $selectedOption . '" does not exist.');
                    }
                } else {
                    if (!empty($selectedOption)) {
                        throw new \Exception($food->name . ' does not have options.');
                    }
                }

                $finalPrice = $food->price + $extraPrice;
                $total += $finalPrice * $qty;

                $validatedItems[] = [
                    'food' => $food,
                    'qty' => $qty,
                    'option' => $selectedOption,
                    'price' => $finalPrice,
                ];
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

            foreach ($validatedItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'food_item_id' => $item['food']->id,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'option' => $item['option'],
                    'status' => 'Pending',
                ]);

                $item['food']->decrement('stock', $item['qty']);
            }

            DB::commit();

            return redirect()->route('orders.my')
                ->with('success', 'Order placed successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
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
        try {
            $request->validate([
                'status' => 'required|in:Pending,Preparing,Delivered',
            ]);

            $order = Order::with('details')->find($id);

            if (!$order) {
                throw new \Exception('Order not found.');
            }

            if ($order->billing_status === 'Requested') {
                throw new \Exception('Cannot update order status. This order has already been requested for billing.');
            }

            if ($order->billing_status === 'Paid') {
                throw new \Exception('Cannot update order status. This order has already been paid.');
            }

            $currentStatuses = $order->details->pluck('status')->unique();
            if ($currentStatuses->contains('Delivered') && $request->status !== 'Delivered') {
                throw new \Exception('Cannot move back from Delivered status. Items have already been served.');
            }

            foreach ($order->details as $detail) {
                $detail->status = $request->status;
                $detail->save();
            }

            return back()->with('success', 'Order batch status updated to ' . $request->status . '.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // =====================================
    // HELPER: GET CURRENT ACTIVE SESSION
    // OR CREATE A NEW ONE
    // =====================================
    private function getOrCreateSessionCode($tableNumber): string
    {
        // Only reuse sessions that are still active
        $activeOrder = Order::where('table_number', $tableNumber)
            ->whereIn('billing_status', ['Ordering', 'Requested'])
            ->latest('id')
            ->first();

        if ($activeOrder) {
            return $activeOrder->session_code;
        }

        // If no active session, create next session number
        $lastOrderForTable = Order::where('table_number', $tableNumber)
            ->latest('id')
            ->first();

        if (!$lastOrderForTable) {
            return 'T' . $tableNumber . '-001';
        }

        $lastSession = $lastOrderForTable->session_code;

        if (preg_match('/T' . $tableNumber . '\-(\d+)/', $lastSession, $matches)) {
            $nextNumber = str_pad(((int) $matches[1]) + 1, 3, '0', STR_PAD_LEFT);
            return 'T' . $tableNumber . '-' . $nextNumber;
        }

        return 'T' . $tableNumber . '-001';
    }

    public function proceedToCounter()
    {
        try {
            $tableNumber = session('table_number');

            if (!$tableNumber) {
                throw new \Exception('No table assigned. Please set the table number first.');
            }

            $activeOrder = Order::where('table_number', $tableNumber)
                ->where('billing_status', 'Ordering')
                ->latest('id')
                ->first();

            if (!$activeOrder) {
                $requestedOrder = Order::where('table_number', $tableNumber)
                    ->where('billing_status', 'Requested')
                    ->latest('id')
                    ->first();

                if ($requestedOrder) {
                    throw new \Exception('Your bill request is already pending. Please wait at the counter.');
                }

                $paidOrder = Order::where('table_number', $tableNumber)
                    ->where('billing_status', 'Paid')
                    ->latest('id')
                    ->first();

                if ($paidOrder) {
                    throw new \Exception('Your session has already been paid.');
                }

                throw new \Exception('No active orders found for this table. Please place an order first.');
            }

            // Check if all items are delivered
            $undeliveredItems = OrderDetail::whereIn('order_id', function ($query) use ($tableNumber, $activeOrder) {
                $query->select('id')
                    ->from('orders')
                    ->where('table_number', $tableNumber)
                    ->where('session_code', $activeOrder->session_code)
                    ->where('billing_status', 'Ordering');
            })
            ->where('status', '!=', 'Delivered')
            ->count();

            if ($undeliveredItems > 0) {
                throw new \Exception('Not all items have been delivered yet. Please wait for your order to be completed.');
            }

            Order::where('table_number', $tableNumber)
                ->where('session_code', $activeOrder->session_code)
                ->where('billing_status', 'Ordering')
                ->update([
                    'billing_status' => 'Requested'
                ]);

            return redirect()->route('orders.my')
                ->with('success', 'Your bill request has been sent. Please proceed to the counter.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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
        try {
            if (!is_numeric($table) || $table < 1) {
                throw new \Exception('Invalid table number.');
            }

            if (empty($session) || !is_string($session)) {
                throw new \Exception('Invalid session code.');
            }

            $request->validate([
                'payment' => 'required|numeric',
            ]);

            $payment = (float) $request->payment;

            if ($payment < 0) {
                throw new \Exception('Payment amount cannot be negative.');
            }

            if ($payment === 0) {
                throw new \Exception('Payment amount must be greater than 0.');
            }

            DB::beginTransaction();

            $orders = Order::where('table_number', $table)
                ->where('session_code', $session)
                ->get();

            if ($orders->isEmpty()) {
                throw new \Exception('Table or session not found.');
            }

            $requestedOrders = $orders->where('billing_status', 'Requested');

            if ($requestedOrders->isEmpty()) {
                $paidOrders = $orders->where('billing_status', 'Paid');
                if (!$paidOrders->isEmpty()) {
                    throw new \Exception('This session has already been paid.');
                }
                throw new \Exception('This session is not ready for payment.');
            }

            $total = $requestedOrders->sum('total_price');

            if ($payment < $total) {
                $shortfall = number_format($total - $payment, 2);
                throw new \Exception('Insufficient payment. Amount short: ₱' . $shortfall);
            }

            $change = $payment - $total;

            foreach ($requestedOrders as $order) {
                $order->update([
                    'payment_amount' => $payment,
                    'change_amount' => $change,
                    'payment_status' => 'Paid',
                    'billing_status' => 'Paid',
                    'status' => 'Completed',
                ]);
            }

            DB::commit();

            // Clear table session so next customer starts fresh
            session()->forget('table_number');

            return back()->with('success', 'Payment confirmed. Table ' . $table . ' is ready for the next customer. Change: ₱' . number_format($change, 2));
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors());
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

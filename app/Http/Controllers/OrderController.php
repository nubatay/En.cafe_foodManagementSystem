<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    
    public function store(Request $request)
    {
        $items = collect($request->items ?? [])->filter(function ($item) {
            return isset($item['qty']) && (int) $item['qty'] > 0;
        })->values()->toArray();

        $request->merge(['items' => $items]);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:food_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
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

                $total += $food->price * $qty;
            }

            $paymentAmount = (float) $request->payment_amount;

            if ($paymentAmount < $total) {
                throw new \Exception('Payment is not enough. Total order amount is ₱' . number_format($total, 2));
            }

            $changeAmount = $paymentAmount - $total;

            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $total,
                'payment_amount' => $paymentAmount,
                'change_amount' => $changeAmount,
                'payment_status' => 'Paid',
                'status' => 'Pending',
                'date' => now(),
            ]);

            foreach ($request->items as $item) {
                $food = FoodItem::lockForUpdate()->findOrFail($item['id']);
                $qty = (int) $item['qty'];

                OrderDetail::create([
                    'order_id' => $order->id,
                    'food_item_id' => $food->id,
                    'qty' => $qty,
                    'price' => $food->price,
                ]);

                $food->decrement('stock', $qty);
            }

            DB::commit();

            return redirect()->route('orders.my')
                ->with('success', 'Order placed successfully. Change: ₱' . number_format($changeAmount, 2));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

   
    public function myOrders()
    {
        $orders = Order::with(['details.food'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('orders.my-orders', compact('orders'));
    }

   
    public function index()
    {
        $orders = Order::with(['user', 'details.food'])
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    
    public function kitchen()
    {
        $orders = Order::with(['user', 'details.food'])
            ->orderByRaw("CASE
                WHEN status = 'Pending' THEN 1
                WHEN status = 'Preparing' THEN 2
                WHEN status = 'Completed' THEN 3
                ELSE 4
            END")
            ->latest()
            ->get();

        return view('orders.kitchen', compact('orders'));
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Preparing,Completed',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->back()
            ->with('success', 'Order status updated successfully.');
    }
}
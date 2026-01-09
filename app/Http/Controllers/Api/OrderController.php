<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreOrderRequest $request)
    {
        $this->authorize('create', Order::class);

        $order = Order::create([
            'tenant_id'    => auth()->user()->tenant_id,
            'user_id'      => auth()->id(),
            'customer_id'  => $request->customer_id,
            'status'       => 'pending', // 👈 correct
            'total_amount' => 0,
        ]);

        $total = 0;

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);

            OrderItem::create([
                'order_id'  => $order->id,
                'product_id'=> $product->id,
                'qty'       => $item['qty'],
                'price'     => $product->price,
            ]);

            $total += $product->price * $item['qty'];
        }

        $order->update(['total_amount' => $total]);

        return response()->json([
            'message' => 'Order created (pending)',
            'order_id' => $order->id,
        ], 201);
    }

    public function pay(Order $order)
    {
        $this->authorize('update', $order);

        if ($order->status !== 'pending') {
            abort(422, 'Only pending orders can be paid');
        }

        DB::transaction(function () use ($order) {

            foreach ($order->items as $item) {

                $product = Product::lockForUpdate()
                    ->findOrFail($item->product_id);

                if ($product->stock < $item->qty) {
                    abort(422, 'Insufficient stock for ' . $product->name);
                }

                $product->decrement('stock', $item->qty);
            }

            $order->update(['status' => 'paid']);
        });

        return response()->json([
            'message' => 'Order paid successfully',
        ]);
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);

        DB::transaction(function () use ($order) {

            if ($order->status === 'paid') {
                foreach ($order->items as $item) {
                    $item->product->increment('stock', $item->qty);
                }
            }

            $order->update(['status' => 'cancelled']);
        });

        return response()->json([
            'message' => 'Order cancelled',
        ]);
    }

}

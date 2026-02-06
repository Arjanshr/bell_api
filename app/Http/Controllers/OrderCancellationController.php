<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderCancellationCategory;
use Illuminate\Http\Request;

class OrderCancellationController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'order_cancellation_category_id' => 'required|exists:order_cancellation_categories,id',
            'reason' => 'nullable|string|max:2000',
        ]);

        if ($order->cancellation) {
            toastr()->info('Order already has a cancellation reason.');
            return redirect()->route('orders');
        }

        $cancellation = new OrderCancellation();
        $cancellation->order_id = $order->id;
        $cancellation->order_cancellation_category_id = $data['order_cancellation_category_id'];
        $cancellation->reason = $data['reason'] ?? null;
        $cancellation->admin_id = $request->user()->id;
        $cancellation->save();

        $order->status = OrderStatus::CANCELLED->value;
        $order->save();

        toastr()->success('Order cancelled and reason recorded.');
        return redirect()->route('orders');
    }
}

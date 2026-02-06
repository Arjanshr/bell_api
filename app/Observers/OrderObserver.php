<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Notification;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $user = $order->customer;
            if (! $user) {
                return;
            }

            $reference = $order->reference_number ?? $order->id;
            $status = ucwords(str_replace('_', ' ', $order->status));
            $message = "Hi! Good news — your order #{$reference} is now {$status}.";

            $exists = Notification::where('user_id', $user->id)
                ->where('message', $message)
                ->exists();

            if (! $exists) {
                Notification::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'message' => $message,
                ]);
            }
        }
    }
}

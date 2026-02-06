<?php

namespace App\Observers;

use App\Models\QuestionsAndAnswer;
use App\Models\Notification;

class QuestionsAndAnswerObserver
{
    /**
     * Handle the QuestionsAndAnswer "updated" event.
     */
    public function updated(QuestionsAndAnswer $qa): void
    {
        $answered = $qa->wasChanged('answer') || ($qa->wasChanged('status') && $qa->status === 'answered');
        if (! $answered) {
            return;
        }

        $user = $qa->user;
        if (! $user) {
            return;
        }

        $product = $qa->product;
        $productName = $product->name ?? 'the product';

        $message = "Your question about '{$productName}' has been answered: '{$qa->answer}'";

        $exists = Notification::where('user_id', $user->id)
            ->where('product_id', $product ? $product->id : null)
            ->where('message', $message)
            ->exists();

        if (! $exists) {
            Notification::create([
                'user_id' => $user->id,
                'product_id' => $product ? $product->id : null,
                'message' => $message,
            ]);
        }
    }
}

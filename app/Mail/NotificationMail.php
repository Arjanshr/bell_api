<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;

class NotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Notification $notification;

    /**
     * Create a new message instance.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        $this->onQueue('emails');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $product = null;
        $campaign = null;
        try {
            if ($this->notification->product_id) {
                $product = \App\Models\Product::find($this->notification->product_id);
            }
            if ($this->notification->campaign_id) {
                $campaign = \App\Models\Campaign::find($this->notification->campaign_id);
            }
        } catch (\Throwable $e) {
            // ignore lookup failures
        }

        return $this->subject('New notification from Mobilemandu')
                    ->view('emails.notification')
                    ->with([
                        'message' => $this->notification->message,
                        'product_slug' => $product->slug ?? null,
                        'campaign_slug' => $campaign->slug ?? null,
                    ]);
    }
}

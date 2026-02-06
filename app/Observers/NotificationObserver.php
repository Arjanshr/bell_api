<?php

namespace App\Observers;

use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Events\NotificationCreated;
use App\Mail\NotificationMail;

class NotificationObserver
{
    /**
     * Handle the Notification "created" event.
     */
    public function created(Notification $notification): void
    {
        // Broadcast event for real-time frontend listeners
        event(new NotificationCreated($notification));

        // Send a simple email to the user if email is available
        try {
            $user = $notification->user;
            if ($user && $user->email) {
                Mail::to($user->email)->queue(new NotificationMail($notification));
            }
        } catch (\Exception $e) {
            // Swallow email errors; they are logged by the mailer
        }
    }
}

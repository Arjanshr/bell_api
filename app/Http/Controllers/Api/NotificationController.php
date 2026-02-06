<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Wishlist;
use App\Models\Campaign;
use App\Models\DeletedNotification;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;

class NotificationController extends BaseController
{
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::with(['product', 'campaign'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalNotifications = Notification::where('user_id', $user->id)->count();
        $totalUnreadNotifications = Notification::where('user_id', $user->id)->where('is_read', false)->count();

        $resource = NotificationResource::collection($notifications);
        $responseData = $resource->response()->getData(true);

        $meta = $responseData['meta'] ?? [];
        $links = $responseData['links'] ?? [];

        $pagination = array_merge($meta, [
            'first_page_url' => $links['first'] ?? null,
            'last_page_url' => $links['last'] ?? null,
            'prev_page_url' => $links['prev'] ?? null,
            'next_page_url' => $links['next'] ?? null,
        ]);

        $dataWrapper = [
            'data' => $responseData['data'] ?? [],
            'pagination' => $pagination,
            'total_notifications' => $totalNotifications,
            'total_unread_notifications' => $totalUnreadNotifications,
        ];

        return response()->json([
            'success' => true,
            'data' => $dataWrapper,
            'message' => 'Notifications retrieved successfully.',
        ], 200);
    }

    public function markAsRead(Notification $notification, Request $request)
    {
        $user = $request->user();
        if ($notification->user_id !== $user->id) {
            return $this->sendError('Unauthorized action', 403);
        }

        if (!$notification->is_read) {
            $notification->is_read = true;
            $notification->save();
        }

        return $this->sendResponse($notification, 'Notification marked as read successfully.');
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $updated = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($updated === 0) {
            return $this->sendError('No unread notifications found', 404);
        }

        return $this->sendResponse([], 'All notifications marked as read successfully.');
    }

    public function deleteNotification(Notification $notification, Request $request)
    {
        $user = $request->user();
        if ($notification->user_id !== $user->id) {
            return $this->sendError('Unauthorized action', 403);
        }

        // Use product_id and campaign_id directly from notification
        if ($notification->product_id && $notification->campaign_id) {
            DeletedNotification::create([
                'user_id' => $user->id,
                'product_id' => $notification->product_id,
                'campaign_id' => $notification->campaign_id,
                'deleted_at' => now(),
            ]);
        }

        $notification->delete();

        return $this->sendResponse([], 'Notification deleted successfully.');
    }

    public function deleteAllNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::where('user_id', $user->id)->get();

        if ($notifications->isEmpty()) {
            return $this->sendError('No notifications found', 404);
        }

        // Track all deleted notifications for campaign/product notifications
        foreach ($notifications as $notification) {
            if ($notification->product_id && $notification->campaign_id) {
                DeletedNotification::create([
                    'user_id' => $user->id,
                    'product_id' => $notification->product_id,
                    'campaign_id' => $notification->campaign_id,
                    'deleted_at' => now(),
                ]);
            }
        }

        Notification::where('user_id', $user->id)->delete();

        return $this->sendResponse([], 'All notifications deleted successfully.');
    }

    public function checkWishlistForCampaigns($user_id)
    {
        $wishlist_items = Wishlist::where('user_id', $user_id)->pluck('product_id');
        $active_campaigns = Campaign::running()->whereHas('products', function ($query) use ($wishlist_items) {
            $query->whereIn('products.id', $wishlist_items);
        })->get();

        foreach ($active_campaigns as $campaign) {
            foreach ($campaign->products as $product) {
                if ($wishlist_items->contains($product->id)) {
                    // Check if notification was previously deleted
                    $deleted = DeletedNotification::where('user_id', $user_id)
                        ->where('product_id', $product->id)
                        ->where('campaign_id', $campaign->id)
                        ->exists();

                    if ($deleted) {
                        continue;
                    }

                    // Check if a notification already exists for this product and campaign
                    $existing_notification = Notification::where('user_id', $user_id)
                        ->where('product_id', $product->id)
                        ->where('campaign_id', $campaign->id)
                        ->exists();

                    if (!$existing_notification) {
                        $message = "The product '{$product->name}' in your wishlist is part of an active campaign: '{$campaign->name}'. Check it out!";
                        Notification::create([
                            'user_id' => $user_id,
                            'message' => $message,
                            'product_id' => $product->id,
                            'campaign_id' => $campaign->id,
                        ]);
                    }
                }
            }
        }
    }
}

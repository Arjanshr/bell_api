<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request)
    {
        return NotificationResource::collection($this->collection);
    }

    /**
     * Add additional data to the resource response (top-level keys).
     */
    public function with($request)
    {
        $userId = $request->user() ? $request->user()->id : ($this->collection->first() ? $this->collection->first()->user_id : null);
        $totalNotifications = $userId ? \App\Models\Notification::where('user_id', $userId)->count() : 0;
        $totalUnreadNotifications = $userId ? \App\Models\Notification::where('user_id', $userId)->where('is_read', 0)->count() : 0;

        $pagination = [];
        if (is_object($this->resource) && method_exists($this->resource, 'total')) {
            $pagination = [
                'total' => $this->resource->total(),
                'count' => $this->resource->count(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'total_pages' => $this->resource->lastPage(),
            ];
        }

        return [
            'total_notifications' => $totalNotifications,
            'total_unread_notifications' => $totalUnreadNotifications,
            'pagination' => $pagination,
        ];
    }
}

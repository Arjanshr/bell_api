<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'message' => $this->message,
            'is_read' => (bool) $this->is_read,
            'product_id' => $this->product_id,
            'campaign_id' => $this->campaign_id,
            'order_id' => $this->order_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product' => $this->whenLoaded('product', function () use ($request) {
                $user = $request->user();
                $product = $this->product;
                if (!$product) {
                    return null;
                }
                $campaign = null;
                if ($this->campaign_id) {
                    $campaign = \App\Models\Campaign::find($this->campaign_id);
                }
                return [
                    "id" => $product->id,
                    "name" => $product->name,
                    "slug" => $product->slug,
                    "rating" => $product->getAverageRating(),
                    "discounted_amount" => $product->discounted_price,
                    "original_amount" => $product->price,
                    "campaign_price" => ($campaign && $campaign->hasStarted()) && $this->campaign_id && $product->pivot
                        ? $product->pivot->campaign_price
                        : '???',
                    "added_to_cart" => false,
                    "added_to_wishlist" => $user
                        ? ($product->id ? $user->hasInWishlist($product->id) : false)
                        : false,
                    "image_link" => $product->getFirstMedia() ? $product->getFirstMedia()->getUrl() : null,
                    "offer" => null,
                    "status" => $product->status,
                    "tags" => [
                        "new" => $product->isNew(),
                        "popular" => $product->isPopular(),
                        "campaign" => $product->isCampaignProduct()->first() ? $product->isCampaignProduct()->first()->name : false,
                    ]
                ];
            }),
            'campaign' => $this->whenLoaded('campaign', function () {
                return $this->campaign ? [
                    'id' => $this->campaign->id,
                    'name' => $this->campaign->name,
                    'slug' => $this->campaign->slug,
                ] : null;
            }),
            'order' => $this->whenLoaded('order', function () {
                return $this->order ? (new \App\Http\Resources\OrderResource($this->order))->toArray(request()) : null;
            }),
        ];
    }
}

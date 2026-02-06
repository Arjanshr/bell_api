<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyReviewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            "product" => [
                "id" => $this->product->id,
                "name" => $this->product->name,
                "slug" => $this->product->slug,
                "average rating" => $this->product->getAverageRating(),
                "discounted_amount" => $this->product->discounted_price,
                "original_amount" => $this->product->price,
                "added_to_cart" => false,
                "added_to_wishlist" => $user
                    ? $user->hasInWishlist($this->product->id)
                    : false,
                "image_link" => $this->product->getFirstMedia() ? $this->product->getFirstMedia()->getUrl() : null,
                "offer" => null,
                "status" => $this->product->status,
                "tags" => [
                    "new" => $this->product->isNew(),
                    "popular" => $this->product->isPopular(),
                    "campaign" => $this->product->isCampaignProduct()->first() ? $this->product->isCampaignProduct()->first()->name : false,
                ],
            ],
            "rating" => $this->rating,
            "review" => $this->review,
            "order_id" => $this->order_id,
        ];
    }
}

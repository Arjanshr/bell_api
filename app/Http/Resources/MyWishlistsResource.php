<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyWishlistsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->product;
        $user = $request->user();

        return [
            "id" => $product ? $product->id : null,
            "name" => $product ? $product->name : null,
            "slug" => $product ? $product->slug : null,
            "rating" => $product ? $product->getAverageRating() : null,
            "discounted_amount" => $product ? $product->discounted_price : null,
            "original_amount" => $product ? $product->price : null,
            "added_to_cart" => false,
            "added_to_wishlist" => $user
                ? $user->hasInWishlist($this->product->id)
                : false,
            "image_link" => $product && $product->getFirstMedia() ? $product->getFirstMedia()->getUrl() : null,
            "offer" => null,
            "alt_text" => $this->alt_text,
            "status" => $product ? $product->status : null,
            "tags" => [
                "new" => $product ? $product->isNew() : false,
                "popular" => $product ? $product->isPopular() : false,
                "campaign" => $product && $product->isCampaignProduct()->first() ? $product->isCampaignProduct()->first()->name : false,
            ]
        ];
    }
}

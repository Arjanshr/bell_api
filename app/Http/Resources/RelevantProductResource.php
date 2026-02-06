<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelevantProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $product = $this->product ?? $this; // Support both Product and wrapper models

        return [
            "id" => $product->id,
            "name" => $product->name,
            "slug" => $product->slug,
            "rating" => $product->getAverageRating(),
            "discounted_amount" => $product->discounted_price,
            "original_amount" => $product->price,
            "added_to_cart" => false,
            "added_to_wishlist" => $user
                ? $user->hasInWishlist($product->id)
                : false,
            "image_link" => $product->getFirstMedia()?->getUrl(),
            "offer" => null,
            "alt_text" => property_exists($this, 'alt_text') ? $this->alt_text : null,
            "status" => $product->status,
            "tags" => [
                "new" => $product->isNew(),
                "popular" => $product->isPopular(),
                "campaign" => optional($product->isCampaignProduct()->first())->name ?? false,
            ]
            ,
            "sold" => $product->sold_count ?? 0,
            "in_stock" => $product->in_stock ?? false,
        ];
    }
}

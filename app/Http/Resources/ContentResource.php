<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        // Handle both Product and relation cases
        $product = $this->product ?? $this;

        if (!$product) {
            return [];
        }

        return [
            "id" => $product->id ?? null,
            "name" => $product->name ?? null,
            "slug" => $product->slug ?? null,
            "rating" => method_exists($product, 'getAverageRating') ? $product->getAverageRating() : null,
            "discounted_amount" => $product->discounted_price ?? null,
            "original_amount" => $product->price ?? null,
            "added_to_cart" => false,
            "added_to_wishlist" => $user
                ? (method_exists($user, 'hasInWishlist') ? $user->hasInWishlist($product->id) : false)
                : false,
            "image_link" => method_exists($product, 'getFirstMedia') && $product->getFirstMedia() ? $product->getFirstMedia()->getUrl() : null,
            "offer" => null,
            "alt_text" => $this->alt_text ?? null,
            "status" => $product->status ?? null,
            "tags" => [
                "new" => method_exists($product, 'isNew') ? $product->isNew() : false,
                "popular" => method_exists($product, 'isPopular') ? $product->isPopular() : false,
                "campaign" => method_exists($product, 'isCampaignProduct') ? optional($product->isCampaignProduct()->first())->name ?? false : false,
            ]
            ,
            "sold" => $product->sold_count ?? 0,
            "in_stock" => $product->in_stock ?? false,
        ];
    }
}

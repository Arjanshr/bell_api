<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Prepare image URLs
        $image_urls = [];
        if ($this->getMedia() && $this->getMedia()->count() > 0) {
            foreach ($this->getMedia() as $image) {
                $image_urls[] = [
                    'url' => $image->getUrl(),
                    'alt_text' => $image->custom_properties['alt_text'] ?? null,
                ];
            }
        }

        // Prepare rating summary
        $rating_summary = [
            'Five' => $this->reviews()->where('rating', 5)->count(),
            'Four' => $this->reviews()->where('rating', 4)->count(),
            'Three' => $this->reviews()->where('rating', 3)->count(),
            'Two' => $this->reviews()->where('rating', 2)->count(),
            'One' => $this->reviews()->where('rating', 1)->count(),
        ];

        // Prepare variants
        $variants = $this->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'stock_quantity' => $variant->stock_quantity,
                'options' => $variant->variant_options->map(function ($option) {
                    return [
                        'specification_name' => $option->specification->name,
                        'value' => $option->value,
                    ];
                }),
            ];
        });
        $user = $request->user();

        return [
            "id" => $this->id,
            "name" => $this->name,
            "short_description" => $this->short_description ?? $this->description,
            "description" => $this->description?$this->convertOembedToIframe($this->description):null,
            "slug" => $this->slug,
            "average_rating" => $this->getAverageRating(),
            "discounted_amount" => $this->discounted_price,
            "original_amount" => $this->price,
            "sold" => $this->sold_count ?? 0,
            "in_stock" => $this->in_stock ?? false,
            "warranty" => $this->warranty,
            "added_to_cart" => false,
            "added_to_wishlist" => $user
                ? $user->hasInWishlist($this->id)
                : false,
            "offer" => null,
            "status" => $this->status,
            "images" => $image_urls,
            "total_reviews" => $this->reviews()->count(),
            "rating_summary" => $rating_summary,
            "category_id" => $this->categories()->first()?->id,
            "category_name" => $this->categories()->first()?->name,
            "category_slug" => $this->categories()->first()?->slug,
            "brand_name" => $this->brand->name,
            "brand_slug" => $this->brand->slug,
            "alt_text" => $this->alt_text,
            "tags" => [
                "new" => $this->isNew(),
                "popular" => $this->isPopular(),
                "campaign" => $this->isCampaignProduct()->first()?->name,
            ],
            "seo" => [
                "meta_title" => $this->meta_title,
                "meta_description" => $this->meta_description,
                "meta_keywords" => $this->keywords,
            ],
            "variants" => $variants, // Include variants here
        ];
    }

    private function convertOembedToIframe(string $description): string
    {
        return preg_replace_callback(
            '/<figure class="media"><oembed url="https:\/\/www\.youtube\.com\/watch\?v=([^"]+)"><\/oembed><\/figure>/',
            function ($matches) {
                $videoId = $matches[1];
                return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>';
            },
            $description
        );
    }
}

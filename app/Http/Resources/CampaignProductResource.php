<?php

namespace App\Http\Resources;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $campaign = Campaign::find($this->pivot->campaign_id)->hasStarted();
        $user = $request->user();

        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "rating" => $this->getAverageRating(),
            "discounted_amount" => $this->discounted_price,
            "original_amount" => $this->price,
            "campaign_price" => $campaign ? $this->pivot->campaign_price : '???',
            "added_to_cart" => false,
            "added_to_wishlist" => $user
                ? $user->hasInWishlist($this->product->id)
                : false,            "image_link" => $this->getFirstMedia() ? $this->getFirstMedia()->getUrl() : null,
            "offer" => null,
            "status" => $this->status,
            "tags"=>[
                "new"=> $this->isNew(),
                "popular"=> $this->isPopular(),
                "campaign"=> $this->isCampaignProduct()->first()?$this->isCampaignProduct()->first()->name:false,
            ]
            ,
            "sold" => $this->sold_count ?? 0,
            "in_stock" => $this->in_stock ?? false,
        ];
    }
}

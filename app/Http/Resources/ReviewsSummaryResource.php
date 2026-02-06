<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewsSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $rating_summary['Five'] = $this->where('rating',5)->count();
        $rating_summary['Four'] = $this->where('rating',4)->count();;
        $rating_summary['Three'] = $this->where('rating',3)->count();;
        $rating_summary['Two'] = $this->where('rating',2)->count();;
        $rating_summary['One'] = $this->where('rating',1)->count();;
        return [
            "total_reviews" => $this->count(),
            "rating_summary" =>  $rating_summary,
        ];
    }
}

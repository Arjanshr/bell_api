<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryBrandImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uploaded' => 1,
            'fileName' => $this['fileName'],
            'url' => asset($this['url']),
        ];
    }
}

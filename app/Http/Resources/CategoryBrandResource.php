<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryBrandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'category' => $this['category']->name,
            'brand' => $this['brand']->name,
            'summary' => $this['summary'],
            'description' => $this['description'],
            "seo" => [
                "meta_title" => $this['meta_title'] ?? null,
                "meta_description" => $this['meta_description'] ?? null,
            ],
        ];
    }
}

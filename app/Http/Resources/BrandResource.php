<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            "summary" => $this->summary,
            "description" => $this->description,
            "slug" => $this->slug,
            "imageLink" => $this->image ? asset('storage/brands/' . $this->image) : asset('images/default.png'),
            "seo" => [
                "meta_title" => $this->meta_title??$this->name,
                "meta_description" => $this->meta_description??strip_tags($this->description),
            ],
        ];
    }
}

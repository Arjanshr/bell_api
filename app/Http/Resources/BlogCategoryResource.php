<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'blogs_count' => $this->blogs_count ?? 0,
            'blogs'       => BlogResource::collection($this->whenLoaded('blogs')),
        ];
    }
}

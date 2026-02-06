<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'title'   => $this->title,
            'slug'    => $this->slug,
            'content' => $this->content,
            'imageLink'   => $this->image?asset('storage/blogs/' . $this->image):asset('images/default.png'),
            'status'  => $this->status,
            'category' => $this->blogCategory ? [
                'id'   => $this->blogCategory->id,
                'name' => $this->blogCategory->name,
                'slug' => $this->blogCategory->slug,
            ] : null,
            'author' => $this->author ? [
                'id'   => $this->author->id,
                'name' => $this->author->name,
                'email' => $this->author->email,
            ] : null,
            'date'=> $this->created_at,
            'date_readable' => $this->created_at->diffForHumans()
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'title'   => $this->title,
            'slug'    => $this->slug,
            'content' => $this->convertOembedToIframe($this->content),
            'imageLink' => $this->image ? asset('storage/blogs/' . $this->image) : asset('images/default.png'),
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
            'date' => $this->created_at,
            'date_readable' => $this->created_at->diffForHumans(),
            "seo" => [
                "meta_title" => $this->meta_title ?? $this->title,
                "meta_description" => (function () {
                    $desc = $this->meta_description ?? strip_tags($this->content);
                    if (mb_strlen($desc) > 300) {
                        return mb_substr($desc, 0, 297) . '...';
                    }
                    return $desc;
                })(),
            ],

        ];
    }

    private function convertOembedToIframe(string $content): string
    {
        return preg_replace_callback(
            '/<figure class="media"><oembed url="https:\/\/www\.youtube\.com\/watch\?v=([^"]+)"><\/oembed><\/figure>/',
            function ($matches) {
                $videoId = $matches[1];
                return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>';
            },
            $content
        );
    }
}

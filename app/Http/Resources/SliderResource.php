<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'link_url' => $this->link_url,
            'display_order' => $this->display_order,
            // Desktop image
            'imageLink' => $this->image ? asset('storage/sliders/' . $this->image) : asset('images/default.png'),
            // Mobile image
            'mobileImageLink' => $this->mobile_image ? asset('storage/sliders/' . $this->mobile_image) : asset('images/default.png'),
        ];
    }
}

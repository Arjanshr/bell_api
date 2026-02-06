<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "order_id" => $this->order->id,
            "product" => $this->product->except(['created_at', 'updated_at','media']),
            "primary_image" =>  $this->product->getFirstMedia() ? $this->product->getFirstMedia()->getUrl() : null,
            "coupon_code" => $this->order->coupon_code,
            "shipping_price" => $this->order->shipping_price,
            "quantity" => $this->quantity,
            "price" => $this->price,
            "discount" => $this->discount,
            "review" => $this->review,
        ];
    }
}

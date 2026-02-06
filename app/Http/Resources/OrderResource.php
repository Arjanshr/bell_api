<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "order_date" => $this->created_at,
            "total_items" => $this->order_items->count(),
            "total_price" => $this->total_price,
            "discount" => $this->discount,
            "shipping_price" => $this->shipping_price,
            "grand_total" => $this->grand_total,
            "coupon_code" => $this->coupon_code,
            "payment_type" => $this->payment_type,
            "payment_status" => $this->payment_status,
            "status" => $this->status,
            "area" => [
                "id" => $this->area->id,
                "name" => $this->area->name,
                "shipping_price" => $this->area->shipping_price,
            ],
            "city" => [
                "id" => $this->area->city->id,
                "name" => $this->area->city->name,
            ],
            "province" => [
                "id" => $this->area->city->province->id,
                "name" => $this->area->city->province->name,
            ],
            "address" => [
                "type"=>$this->address->type,
                "location"=>$this->address->location,
                "phone_number"=>$this->address->phone_number,
            ],
            "shipping_address" => $this->shipping_address,
            "cancellable" => $this->status == 'pending',
        ];
    }
}

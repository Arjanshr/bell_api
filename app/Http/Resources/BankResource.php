<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'min_emi_price' => $this->min_emi_price,
            'tenures' => TenureResource::collection($this->whenLoaded('tenures')),
        ];
    }
}

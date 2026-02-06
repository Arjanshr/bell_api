<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            "name" => $this->user->name,
            "type" => $this->type,
            "province" => $this->province,
            "city" => $this->city,
            "area" => $this->area,
            "location" => $this->location,
            "phone_number" => $this->phone_number,
            "is_default" => $this->is_default
        ];
    }
}

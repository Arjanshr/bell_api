<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenureResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'months' => $this->months,
            'service_charge_percent' => $this->service_charge_percent,
        ];
    }
}

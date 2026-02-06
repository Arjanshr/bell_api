<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EMICalculationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'financed_amount' => round($this['financed_amount'], 2),
            'service_charge' => round($this['service_charge'], 2),
            'processing_fee' => round($this['processing_fee'], 2),
            'emi' => round($this['emi'], 2),
            'tenure_months' => $this['tenure_months'],
            'bank_name' => $this['bank_name'],
        ];
    }
}

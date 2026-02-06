<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponApplyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'message'       => 'Coupon applied successfully',
            'shipping_price'=>$this->shipping_price,
            'discount'      => $this->total_discount,
            'new_total'     => $this->new_total,
            'updated_items' => $this->updated_items
        ];
    }
}

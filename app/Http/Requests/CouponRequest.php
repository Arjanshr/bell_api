<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Change if needed
    }

    public function rules()
    {
        $couponId = $this->route('coupon') ? $this->route('coupon')->id : null;

        return [
            'code' => 'required|string|unique:coupons,code,' . $couponId,
            'type' => 'required|in:fixed,percentage',
            'discount' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_user_specific' => 'nullable', // Allow null instead of boolean
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'specific_type' => 'nullable',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'brand_ids' => 'nullable|array',
            'brand_ids.*' => 'exists:brands,id', // Ensure valid brand IDs
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id', // Ensure valid product IDs
            'status' => 'required|boolean',
            'start_datetime' => 'nullable|date', // Allow start_datetime to be nullable
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Coupon code is required.',
            'code.unique' => 'This coupon code already exists.',
            'discount.required' => 'Discount value is required.',
            'type.required' => 'Discount type is required.',
            'status.required' => 'Coupon status is required.',
            'user_ids.*.exists' => 'Selected user does not exist.',
            'category_ids.*.exists' => 'Selected category does not exist.',
            'brand_ids.*.exists' => 'Selected brand does not exist.', // New message for brand
            'product_ids.*.exists' => 'Selected product does not exist.', // New message for product
        ];
    }
}

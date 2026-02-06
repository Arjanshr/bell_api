<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponApplyResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends BaseController {
    // Check if the coupon code is the hardcoded bypass coupon
    private function isBypassCoupon($coupon_code)
    {
        return $coupon_code === 'FREEDELIVERY';
    }

    // Check if any item matches the bypass product IDs
    private function hasBypassProduct($items)
    {
        $bypass_product_ids = [3494, 3495, 3496];
        foreach ($items as $item) {
            if (in_array($item['id'], $bypass_product_ids)) {
                return true;
            }
        }
        return false;
    }

    // Return the bypass coupon response
    private function applyBypassCouponResponse($data)
    {
        $original_shipping_price = $data['shipping_price'];
        $cart_total_without_shipping = $data['cart_total'] - $original_shipping_price;
        $shipping_price = 0;
        $total_discount = 0;
        $updated_items = $data['items'];
        $new_total = ceil($cart_total_without_shipping + $shipping_price);
        return $this->sendResponse(
            new CouponApplyResource((object) [
                'shipping_price' => $shipping_price,
                'total_discount' => $total_discount,
                'new_total' => $new_total,
                'updated_items' => $updated_items
            ]),
            'Free delivery coupon applied.'
        );
    }

    public function applyCoupon(Request $request)
    {

        $data = $this->validateCouponRequest($request);
        if ($this->isBypassCoupon($data['coupon_code']) && $this->hasBypassProduct($data['items'])) {
            return $this->applyBypassCouponResponse($data);
        }

        // --- Original logic below ---
        // Get the valid coupon and check its validity
        $coupon = $this->getValidCoupon($data['coupon_code']);
        if (!$coupon) {
            return $this->sendError('Invalid coupon', 'This coupon is either invalid, expired, or restricted.', 400);
        }

        // Check if the coupon is user-specific
        if ($coupon->is_user_specific && !$this->isValidForUser($coupon)) {
            return $this->sendError('Invalid coupon', 'This coupon is restricted or already used.', 400);
        }

        // Store original shipping price
        $original_shipping_price = $data['shipping_price'];

        // Apply free delivery coupon (set shipping price to zero)
        $shipping_price = ($coupon->specific_type === 'free_delivery') ? 0 : $original_shipping_price;

        // Get valid specific IDs based on the coupon's specific type
        $valid_specific_ids = $this->getValidSpecificIds($coupon);
        $is_specific_type = !empty($valid_specific_ids);

        // Apply the coupon to each item and calculate the total discount
        list($total_discount, $updated_items) = $this->applyDiscountToItems($data['items'], $coupon, $valid_specific_ids, $is_specific_type);

        // Since cart_total already includes the original shipping fee, remove it before recalculating
        $cart_total_without_shipping = $data['cart_total'] - $original_shipping_price;

        // Calculate the new total (excluding original shipping, then adding updated shipping fee)
        $new_total = ceil($cart_total_without_shipping - $total_discount + $shipping_price);

        return $this->sendResponse(
            new CouponApplyResource((object) [
                'shipping_price' => $shipping_price, // Updated shipping price
                'total_discount' => $total_discount,
                'new_total' => $new_total,
                'updated_items' => $updated_items
            ]),
            'Coupon applied successfully.'
        );
    }



    // Validate the coupon apply request
    private function validateCouponRequest(Request $request)
    {
        return $request->validate([
            'coupon_code'  => 'required|string',
            'cart_total'   => 'required|numeric|min:0',
            'items'         => 'required|array',
            'items.*.id'    => 'required|integer',
            'items.*.rate'  => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_price' => 'required|numeric|min:0'
        ]);
    }

    // Get the valid coupon by its code
    private function getValidCoupon($coupon_code)
    {
        $coupon = Coupon::where('code', $coupon_code)
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->first();
        if (!$coupon || $this->hasExceededMaxUses($coupon)) {
            return null;
        }

        if ($coupon->start_datetime && now()->lt($coupon->start_datetime)) {
            return null;
        }
        return $coupon;
    }

    // Get valid specific IDs based on the coupon's specific_type (category, brand, or product)
    private function getValidSpecificIds(Coupon $coupon)
    {
        $valid_specific_ids = [];

        // Based on the coupon's specific type, get the corresponding valid IDs
        if ($coupon->specific_type == 'category') {
            $valid_specific_ids = $coupon->categories->pluck('id')->toArray();
        } elseif ($coupon->specific_type == 'brand') {
            $valid_specific_ids = $coupon->brands->pluck('id')->toArray();
        } elseif ($coupon->specific_type == 'product') {
            $valid_specific_ids = $coupon->products->pluck('id')->toArray(); // Fixed product relation
        }

        return $valid_specific_ids;
    }

    // Apply the coupon discount to each item
    private function applyDiscountToItems($items, $coupon, $valid_specific_ids, $is_specific_type)
    {
        $total_discount = 0;
        $updated_items = [];

        foreach ($items as $item) {
            $product = Product::with('categories', 'brand')->find($item['id']);
            if (!$product) continue;

            // Check eligibility based on the coupon's specific_type or apply to all if type is "normal"
            $is_eligible = $coupon->specific_type === 'normal' || $this->isItemEligible($product, $coupon->specific_type, $valid_specific_ids);

            // Calculate discount and update item details
            $item['discount'] = $is_eligible ? $this->calculateDiscount($coupon, $item) : 0;
            $item['total'] = ceil(($item['rate'] * $item['quantity']) - $item['discount']);

            $total_discount += $item['discount'];
            $updated_items[] = $item;
        }

        return [$total_discount, $updated_items];
    }

    // Check if the item is eligible based on the coupon's specific_type (category, brand, product)
    private function isItemEligible($product, $specific_type, $valid_specific_ids)
    {
        if ($specific_type == 'category') {
            $product_category_ids = $product->categories->pluck('id')->toArray();
            return !empty(array_intersect($product_category_ids, $valid_specific_ids));
        } elseif ($specific_type == 'brand') {
            return in_array($product->brand_id, $valid_specific_ids); // Fixed brand check
        } elseif ($specific_type == 'product') {
            return in_array($product->id, $valid_specific_ids);
        }

        return false;
    }

    // Check if the coupon is valid for the user
    private function isValidForUser($coupon)
    {
        $user_id = Auth::id();

        // Check if the coupon is user-specific
        if ($coupon->is_user_specific && $coupon->users()->exists() && !$coupon->users->contains($user_id)) {
            return false;
        }

        // Prevent user from reusing the same coupon in another order
        if (Order::where('user_id', $user_id)->where('coupon_code', $coupon->code)->exists()) {
            return false;
        }

        // Check if the coupon has exceeded maximum usage
        if ($coupon->uses >= $coupon->max_uses) {
            return false;
        }

        return true;
    }

    // Calculate the discount for an item
    private function calculateDiscount($coupon, $item)
    {
        $item_total = $item['rate'] * $item['quantity'];
        $discount = ($coupon->type === 'fixed') ? min($coupon->discount, $item_total) : ($item_total * $coupon->discount) / 100;

        return ceil(min($discount, $coupon->max_discount ?? $discount));
    }

    private function hasExceededMaxUses(Coupon $coupon)
    {
        // If max_uses is null, the coupon has unlimited uses
        if ($coupon->max_uses === null) {
            return false;
        }

        // Check if the coupon's use count has reached the maximum allowed uses
        return $coupon->uses >= $coupon->max_uses;
    }
}

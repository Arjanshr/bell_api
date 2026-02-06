<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\AreaResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\ProvinceResource;
use App\Http\Resources\ShippingPriceResource;
use App\Models\Address;
use App\Models\Area;
use App\Models\City;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCreated;
use App\Models\Campaign;

class OrderController extends BaseController
{
    // Check if the coupon code is the hardcoded bypass coupon
    private function isBypassCoupon($coupon_code)
    {
        return $coupon_code === 'FREEDELIVERY';
    }

    // Apply the bypass coupon logic for specific product IDs
    private function applyBypassCouponLogic($final_items, $area)
    {
        $bypass_product_ids = [3494, 3495, 3496];
        $updated_items = $final_items;
        $has_eligible_product = false;
        foreach ($final_items as $idx => $item) {
            if (in_array($item['product_id'], $bypass_product_ids)) {
                $has_eligible_product = true;
                $updated_items[$idx]['coupon_discount'] = $area->shipping_price ?? 0;
                $updated_items[$idx]['discount'] = 0;
            } else {
                $updated_items[$idx]['coupon_discount'] = 0;
                $updated_items[$idx]['discount'] = 0;
            }
        }
        $shipping_price = $has_eligible_product ? 0 : ($area->shipping_price ?? 0);
        $coupon_discount = $has_eligible_product ? ($area->shipping_price ?? 0) : 0;
        return [$coupon_discount, $shipping_price, $updated_items, $has_eligible_product];
    }
    public function create(OrderRequest $request)
    {
        DB::beginTransaction();
        try {
            // 1. Get or create customer
            $customer = $this->getOrCreateCustomer($request);

            if ($this->hasExceededOrderLimit($customer)) {
                return $this->sendError('You have reached the limit of 10 orders per hour.');
            }

            // 2. Get or create address
            $address = $this->getOrCreateAddress($request, $customer);
            $area = Area::findOrFail($request->area_id);
            $city = City::findOrFail($request->city_id);
            $province = Province::findOrFail($request->province_id);

            $shipping_address = $this->formatShippingAddress($request, $area, $city, $province);

            // 3. Calculate order items
            [$total_price, $total_discount, $final_items] = $this->calculateOrderItems($request->items);

            // 4. Apply free delivery campaigns
            [$final_items, $shipping_price, $campaign_discount, $freeDeliveryApplied] =
                $this->applyFreeDeliveryCampaign($final_items, $total_price, $area);

            // 5. Apply user coupon only if no free delivery campaign applied
            if (!$freeDeliveryApplied) {
                [$coupon_discount, $shipping_price, $final_items] = $this->applyCouponLogic(
                    $request->coupon_code,
                    $customer,
                    $area,
                    $final_items
                );
            } else {
                $coupon_discount = $campaign_discount;
            }

            // 6. Recalculate totals properly
            $total_product_discount = array_sum(array_column($final_items, 'discount')); // product discounts
            $total_coupon_discount = array_sum(array_column($final_items, 'coupon_discount')); // coupon/campaign discounts

            $grand_total = $total_price - $total_product_discount - $total_coupon_discount + $shipping_price;

            // 7. Create the order
            $order = Order::create([
                'user_id' => $customer->id,
                'address_id' => $address->id,
                'total_price' => $total_price,
                'discount' => $total_product_discount,
                'shipping_price' => $shipping_price ?? $area->shipping_price ?? 0,
                'grand_total' => $grand_total,
                'payment_type' => $request->payment_type,
                'payment_status' => 'unpaid',
                'shipping_address' => $shipping_address,
                'area_id' => $area->id,
                'coupon_code' => $request->coupon_code,
                'coupon_discount' => $total_coupon_discount,
            ]);

            $order->reference_number = 'ORDER-' . $order->id;

            // 8. Flag orders made via Postman
            $user_agent = $request->header('User-Agent');
            if (Str::contains($user_agent, 'PostmanRuntime')) {
                $order->is_flagged = true;
                $order->flag_reason = 'Requested via Postman';
            }
            $order->save();

            // 9. Save order items
            foreach ($final_items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'coupon_discount' => $item['coupon_discount'] ?? 0,
                ]);
            }

            // 10. Send email confirmation
            $order->load(['order_items.product', 'customer']);
            Mail::to([$customer->email, 'order_notification@mobilemandu.com'])
                ->send(new OrderCreated($order));

            DB::commit();

            return $this->sendResponse([
                'reference_number' => $order->reference_number,
                'grand_total' => $grand_total,
            ], 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to create order', ['error' => $e->getMessage()]);
        }
    }



    private function applyFreeDeliveryCampaign($final_items, $total_price, $area)
    {
        $activeFreeDeliveryCampaigns = Campaign::running()
            ->where('type', 'free_delivery')
            ->get();

        $shipping_price = $area->shipping_price ?? 0;
        $coupon_discount = 0;
        $freeDeliveryApplied = false;

        foreach ($activeFreeDeliveryCampaigns as $campaign) {
            // Apply free delivery if campaign has no products and min cart total is met
            if ($campaign->products->isEmpty() && $total_price >= ($campaign->min_cart_value ?? 0)) {
                $shipping_price = 0; // Free shipping
                $coupon_discount = 0; // Do NOT add shipping again to coupon discount

                // Ensure product discounts remain untouched
                foreach ($final_items as $idx => $item) {
                    $final_items[$idx]['coupon_discount'] = 0;
                    $final_items[$idx]['discount'] = $final_items[$idx]['discount'] ?? 0;
                }

                $freeDeliveryApplied = true;
                break; // Only one free delivery campaign applied
            }
        }

        return [$final_items, $shipping_price, $coupon_discount, $freeDeliveryApplied];
    }



    private function getOrCreateCustomer($request)
    {
        if (!$request->customer_id) {
            $customer = User::where('email', $request->email)
                ->orWhere('phone', $request->phone_number)
                ->first();
            if (!$customer) {
                $customer = User::create([
                    'name' => $request->reciever_name,
                    'email' => $request->email,
                    'phone' => $request->phone_number,
                    'password' => bcrypt('password'),
                ]);
                $customer->assignRole('customer');
            }
        } else {
            $customer = User::findOrFail($request->customer_id);
        }
        return $customer;
    }

    private function hasExceededOrderLimit($customer)
    {
        $orderCountLastHour = Order::where('user_id', $customer->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();
        return $orderCountLastHour >= 10;
    }

    private function getOrCreateAddress($request, $customer)
    {
        if (!$request->address_id) {
            return Address::firstOrCreate([
                'user_id' => $customer->id,
                'type' => $request->address_type,
                'city_id' => $request->city_id,
                'province_id' => $request->province_id,
                'area_id' => $request->area_id,
                'location' => $request->location,
            ], [
                'phone_number' => $request->phone_number,
            ]);
        } else {
            return Address::findOrFail($request->address_id);
        }
    }

    private function formatShippingAddress($request, $area, $city, $province)
    {
        $address_type = ucfirst($request->address_type);
        $shipping_address  = "Name: {$request->reciever_name}";
        $shipping_address .= "<br/>Phone: {$request->phone_number}";
        $shipping_address .= "<br/>Email: {$request->email}";
        $shipping_address .= "<br/><br/>{$area->name} ({$address_type})";
        $shipping_address .= "<br/> {$request->location}";
        $shipping_address .= "<br/> {$city->name}";
        $shipping_address .= "<br/> {$province->name}";
        return $shipping_address;
    }

    private function calculateOrderItems($items)
    {
        $total_price = 0;
        $total_discount = 0;
        $final_items = [];
        foreach ($items as $item) {
            $product = Product::findOrFail($item['id']);
            $variant = isset($item['variant_id']) ? ProductVariant::find($item['variant_id']) : null;
            $price = $variant ? $variant->price : $product->discounted_price;
            $quantity = $item['quantity'];
            $item_discount = $product->discount ?? 0;
            $total_price += $price * $quantity;
            $total_discount += $item_discount * $quantity;
            $final_items[] = [
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $quantity,
                'price' => $price,
                'discount' => $item_discount,
            ];
        }
        return [$total_price, $total_discount, $final_items];
    }

    private function applyCouponLogic($coupon_code, $customer, $area, $final_items)
    {
        $coupon_discount = 0;
        $shipping_price = $area->shipping_price ?? 0;
        $updated_items = $final_items;

        // Bypass coupon logic
        if ($this->isBypassCoupon($coupon_code)) {
            [$coupon_discount, $shipping_price, $updated_items, $has_eligible_product] = $this->applyBypassCouponLogic($final_items, $area);
            if ($has_eligible_product) {
                return [$coupon_discount, $shipping_price, $updated_items];
            }
        }

        if (!$coupon_code) {
            return [$coupon_discount, $shipping_price, $updated_items];
        }

        $coupon = Coupon::where('code', $coupon_code)
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$coupon || !$coupon->isValidForUser($customer->id)) {
            return [$coupon_discount, $shipping_price, $updated_items];
        }

        // Skip free-delivery coupon if shipping is already 0
        if ($coupon->specific_type === 'free_delivery' && $shipping_price == 0) {
            return [$coupon_discount, $shipping_price, $updated_items];
        }

        if ($coupon->specific_type === 'free_delivery') {
            $shipping_price = 0;
            $coupon_discount = $area->shipping_price ?? 0;
            if (!empty($updated_items)) {
                // Apply discount to first item for tracking (optional)
                $updated_items[0]['coupon_discount'] = $coupon_discount;
            }
            $coupon->increment('uses');
            return [$coupon_discount, $shipping_price, $updated_items];
        }

        // For normal, category, brand, product-specific coupons
        $valid_specific_ids = [];
        if ($coupon->specific_type == 'category') {
            $valid_specific_ids = $coupon->categories->pluck('id')->toArray();
        } elseif ($coupon->specific_type == 'brand') {
            $valid_specific_ids = $coupon->brands->pluck('id')->toArray();
        } elseif ($coupon->specific_type == 'product') {
            $valid_specific_ids = $coupon->products->pluck('id')->toArray();
        }

        $total_coupon_discount = 0;
        foreach ($final_items as $idx => $item) {
            $product = Product::with('categories', 'brand')->find($item['product_id']);
            $is_eligible = match ($coupon->specific_type) {
                'normal' => true,
                'category' => !empty(array_intersect($product->categories->pluck('id')->toArray(), $valid_specific_ids)),
                'brand' => in_array($product->brand_id, $valid_specific_ids),
                'product' => in_array($product->id, $valid_specific_ids),
                default => false,
            };

            $item_total = $item['price'] * $item['quantity'];
            $discount = 0;
            if ($is_eligible) {
                $discount = $coupon->type === 'fixed'
                    ? min($coupon->discount, $item_total)
                    : ($item_total * $coupon->discount / 100);

                $discount = ceil(min($discount, $coupon->max_discount ?? $discount));
            }

            $updated_items[$idx]['coupon_discount'] = $discount;
            $updated_items[$idx]['discount'] = $discount;
            $total_coupon_discount += $discount;
        }

        $coupon_discount = $total_coupon_discount;
        $coupon->increment('uses');

        return [$coupon_discount, $shipping_price, $updated_items];
    }


    public function getProvinces()
    {
        $provinces = Province::get();
        return $this->sendResponse(ProvinceResource::collection($provinces), 'Provinces Retrived successfully.');
    }

    public function getCities($province_id = null)
    {
        if (!$province_id) {
            $cities = City::get();
        } else {
            $cities = City::where('province_id', $province_id)->get();
        }
        return $this->sendResponse(CityResource::collection($cities), 'Cities Retrived successfully.');
    }
    public function getAreas($city_id = null)
    {
        if (!$city_id) {
            $areas = Area::get();
        } else {
            $areas = Area::where('city_id', $city_id)->get();
        }
        return $this->sendResponse(AreaResource::collection($areas), 'Cities Retrived successfully.');
    }

    public function getShippingPrice(Area $area)
    {
        return $this->sendResponse(ShippingPriceResource::make($area), 'Shipping Price retrived successfully.');
    }

    public function applyCoupon(Request $request)
    {
        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json(['message' => 'Invalid or expired coupon'], 400);
        }

        if (!$coupon->isValidForUser($request->user_id)) {
            return response()->json(['message' => 'This coupon is not available for you'], 400);
        }

        $cartItems = $request->items;
        $couponCategories = $coupon->categories->pluck('id')->toArray();

        $discount = 0;
        foreach ($cartItems as $item) {
            if (empty($couponCategories) || in_array($item['category_id'], $couponCategories)) {
                $discount += $coupon->type === 'fixed'
                    ? min($coupon->discount, $item['price'] * $item['quantity'])
                    : ($item['price'] * $item['quantity']) * ($coupon->discount / 100);
            }
        }

        if ($discount == 0) {
            return response()->json(['message' => 'No eligible items for this coupon'], 400);
        }

        return response()->json([
            'discount' => $discount,
            'new_total' => $request->cart_total - $discount
        ]);
    }
}

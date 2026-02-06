<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponSpecific;
use App\Models\User;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
    $coupons = Coupon::latest()->paginate(10);
    return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $users = User::all();
        $categories = Category::all();
        return view('admin.coupons.form', compact('users', 'categories'));
    }

    public function edit(Coupon $coupon)
    {
        $users = User::all();
        $categories = Category::all();
        return view('admin.coupons.form', compact('coupon', 'users', 'categories'));
    }

    public function show(Coupon $coupon)
    {
        $coupon_usage_count = \App\Models\Order::where('coupon_code', $coupon->code)->count(); // Count all orders using this coupon
        $total_discount = \App\Models\Order::where('coupon_code', $coupon->code)
            ->where('status', 'completed') // Only include delivered orders
            ->sum('coupon_discount');
        return view('admin.coupons.show', compact('coupon', 'coupon_usage_count', 'total_discount'));
    }

    public function insert(CouponRequest $request)
    {
        $coupon = Coupon::create([
            'code' => $request->code,
            'type' => $request->type,
            'discount' => $request->discount,
            'start_datetime' => $request->start_datetime ?? now(), // Default to now if not provided
            'max_uses' => $request->max_uses ?? null,
            'expires_at' => $request->expires_at ?? null,
            'status' => $request->status,
            'specific_type' => $request->specific_type,
            'is_user_specific' => $request->has('is_user_specific'),
        ]);

        if ($coupon->is_user_specific && $request->user_ids) {
            $coupon->users()->sync($request->user_ids);
        }

        if ($request->specific_type && $request->specific_ids) {
            foreach ($request->specific_ids as $specific_id) {
                CouponSpecific::create([
                    'coupon_id' => $coupon->id,
                    'specific_id' => $specific_id,
                    'specific_type' => $request->specific_type,
                ]);
            }
        }

        toastr()->success('Coupon created successfully!');
        return redirect()->route('coupons')->with('message', 'Coupon created successfully!');
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        // return $request; // Debugging line, remove in production
        $coupon->update([
            'code' => $request->code,
            'type' => $request->type,
            'discount' => $request->discount,
            'start_datetime' => $request->start_datetime ?? now(), // Default to now if not provided
            'max_uses' => $request->max_uses ?? null,
            'expires_at' => $request->expires_at ?? null,
            'status' => $request->status,
            'specific_type' => $request->specific_type,
            'is_user_specific' => $request->has('is_user_specific'),
        ]);

        if ($coupon->is_user_specific && $request->user_ids) {
            $coupon->users()->sync($request->user_ids);
        } else {
            $coupon->users()->detach();
        }

        CouponSpecific::where('coupon_id', $coupon->id)->delete();
        if ($request->specific_type && $request->specific_ids) {
            foreach ($request->specific_ids as $specific_id) {
                CouponSpecific::create([
                    'coupon_id' => $coupon->id,
                    'specific_id' => $specific_id,
                    'specific_type' => $request->specific_type,
                ]);
            }
        }

        toastr()->success('Coupon updated successfully!');
        return redirect()->route('coupons')->with('message', 'Coupon updated successfully!');
    }

    public function delete(Coupon $coupon)
    {
        CouponSpecific::where('coupon_id', $coupon->id)->delete();
        $coupon->delete();
        toastr()->success('Coupon deleted successfully!');
        return redirect()->route('coupons')->with('success', 'Coupon deleted successfully.');
    }

    public function orders(Coupon $coupon)
    {
        $orders = \App\Models\Order::where('coupon_code', $coupon->code)->paginate(10);
        return view('admin.coupons.orders', compact('coupon', 'orders'));
    }
}
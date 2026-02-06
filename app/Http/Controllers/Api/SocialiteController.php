<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Models\Wishlist;
use App\Models\Campaign;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends BaseController
{
    public function loginSocial(?string $provider = null)
    {
        if ($provider == 'facebook') {
            $data['FACEBOOK_CLIENT_ID'] = env('FACEBOOK_CLIENT_ID');
            $data['FACEBOOK_CLIENT_SECRET'] = env('FACEBOOK_CLIENT_SECRET');
            $data['FACEBOOK_REDIRECT_URI'] = env('FACEBOOK_REDIRECT_URI');
            $data['FACEBOOK_CONFIG_ID'] = env('FACEBOOK_CONFIG_ID');
        } elseif ($provider == 'google') {
            $data['GOOGLE_CLIENT_ID'] = env('GOOGLE_CLIENT_ID');
            $data['GOOGLE_CLIENT_SECRET'] = env('GOOGLE_CLIENT_SECRET');
            $data['GOOGLE_REDIRECT_URI'] = env('GOOGLE_REDIRECT_URI');
        } else {
            $data['facebook']['FACEBOOK_CLIENT_ID'] = env('FACEBOOK_CLIENT_ID');
            $data['facebook']['FACEBOOK_CLIENT_SECRET'] = env('FACEBOOK_CLIENT_SECRET');
            $data['facebook']['FACEBOOK_REDIRECT_URI'] = env('FACEBOOK_REDIRECT_URI');
            $data['facebook']['FACEBOOK_CONFIG_ID'] = env('FACEBOOK_CONFIG_ID');
            $data['google']['GOOGLE_CLIENT_ID'] = env('GOOGLE_CLIENT_ID');
            $data['google']['GOOGLE_CLIENT_SECRET'] = env('GOOGLE_CLIENT_SECRET');
            $data['google']['GOOGLE_REDIRECT_URI'] = env('GOOGLE_REDIRECT_URI');
        }
        return $this->sendResponse($data, 'Data retrived successfully.');
    }

    public function callbackSocial(Request $request, $provider)
    {
        $providers['provider'] = $provider;
        $data = [];
        $this->validateProvider($providers);
        if ($request->email && ($request->email != '' || $request->email != null)) {
            $user = User::firstOrCreate(['email' => $request->email]);
            if (! $user->hasVerifiedEmail() && $request->email) {
                $user->markEmailAsVerified();
            }

            if ($user->wasRecentlyCreated) {
                $user->assignRole('customer');
                $data[$provider . '_id'] = $request->provider_id;
                $data['name'] = $request->name ?? $request->nickname ?? 'Guest User';
                event(new Registered($user));
            }
        } elseif ($request->phone && ($request->phone != '' || $request->phone != null)) {
            $user = User::firstOrCreate(
                ['phone' => $request->phone],
            )->assignRole('customer');
            if ($user->wasRecentlyCreated) {
                $data[$provider . '_id'] = $request->provider_id;
                $data['name'] = $request->name ?? $request->nickname;
                event(new Registered($user));
            }
        } else {
            $user = User::firstOrCreate(
                [$provider . '_id' => $request->provider_id],
            )->assignRole('customer');
            if ($user->wasRecentlyCreated) {
                $data['email'] = $request->email ?? null;
                $data['name'] = $request->name ?? $request->nickname;
                event(new Registered($user));
            }
        }

        if ($request->avatar_url)
            $data['avatar'] = $request->avatar_url;
        $user->update($data);

        // Check wishlist for campaigns and add notifications
        $this->checkWishlistForCampaigns($user->id);

        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User login successfully.');
    }

    protected function checkWishlistForCampaigns($userId)
    {
        $wishlistItems = Wishlist::where('user_id', $userId)->pluck('product_id');
        $activeCampaigns = Campaign::running()->whereHas('products', function ($query) use ($wishlistItems) {
            $query->whereIn('products.id', $wishlistItems); // Specify 'products.id' to avoid ambiguity
        })->get();

        foreach ($activeCampaigns as $campaign) {
            foreach ($campaign->products as $product) {
                if ($wishlistItems->contains($product->id)) {
                    // Check if a notification already exists for this product and campaign
                    $existingNotification = Notification::where('user_id', $userId)
                        ->where('message', "The product '{$product->name}' in your wishlist is part of an active campaign: '{$campaign->name}'. Check it out!")
                        ->exists();

                    if (!$existingNotification) {
                        $message = "The product '{$product->name}' in your wishlist is part of an active campaign: '{$campaign->name}'. Check it out!";
                        Notification::create([
                            'user_id' => $userId,
                            'message' => $message,
                        ]);
                    }
                }
            }
        }
    }

    protected function validateProvider($provider): array
    {
        return $this->getValidationFactory()->make(
            $provider,
            ['provider' => 'in:facebook,google,github']
        )->validate();
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campaign;
use App\Models\Wishlist;
use App\Models\Notification;

class CheckWishlistCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wishlist:check-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check running campaigns and notify users whose wishlist items are in those campaigns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking running campaigns for wishlist matches...');

        $campaigns = Campaign::running()->with('products')->get();

        foreach ($campaigns as $campaign) {
            $productIds = $campaign->products->pluck('id')->toArray();
            if (empty($productIds)) {
                continue;
            }

            $wishlistItems = Wishlist::with('user','product')
                ->whereIn('product_id', $productIds)
                ->get();

            foreach ($wishlistItems as $item) {
                $user = $item->user;
                $product = $item->product;
                if (! $user || ! $product) {
                    continue;
                }

                $exists = Notification::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->where('campaign_id', $campaign->id)
                    ->exists();

                if (! $exists) {
                    $message = "The product '{$product->name}' in your wishlist is part of an active campaign: '{$campaign->name}'. Check it out!";
                    Notification::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'campaign_id' => $campaign->id,
                        'message' => $message,
                    ]);
                    $this->line("Notified user {$user->id} about product {$product->id} for campaign {$campaign->id}");
                }
            }
        }

        $this->info('Wishlist campaign check completed.');
        return 0;
    }
}

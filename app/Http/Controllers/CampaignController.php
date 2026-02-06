<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampaignRequest;
use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::orderBy('display_order', 'asc')->get(); // Order by display_order
        return view('admin.campaign.index', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.campaign.form');
    }

    public function insert(CampaignRequest $request)
    {
        // Increment display_order of all existing campaigns
        Campaign::query()->increment('display_order');

        $campaign = new Campaign();
        $campaign->name = $request->name;
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->status = $request->status;
        $campaign->color_theme = $request->color_theme;
        $campaign->display_order = 1; // Set the new campaign as the first position

        // Campaign type & min cart value
        $campaign->type = $request->type;
        $campaign->min_cart_value = $request->type === 'free_delivery' ? $request->min_cart_value : null;

        // Handle flash sale fields
        $campaign->has_active_period = $request->has_active_period ?? false;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            $campaign->background_image = $request->file('background_image')->store('campaigns', 'public');
        }

        // Handle campaign banner upload
        if ($request->hasFile('campaign_banner')) {
            $campaign->campaign_banner = $request->file('campaign_banner')->store('campaign_banners', 'public');
            $campaign->banner_url = $request->banner_url;
        }

        $campaign->save();

        toastr()->success('Campaign Created Successfully!');
        return redirect()->route('campaigns');
    }



    public function edit(Campaign $campaign)
    {
        return view('admin.campaign.form', compact('campaign'));
    }

    public function update(CampaignRequest $request, Campaign $campaign)
    {
        $campaign->name = $request->name;
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->status = $request->status;
        $campaign->color_theme = $request->color_theme;
        $campaign->slug = $request->slug;

        // Campaign type & min cart value
        $campaign->type = $request->type;
        $campaign->min_cart_value = $request->type === 'free_delivery' ? $request->min_cart_value : null;

        // Handle flash sale fields
        $campaign->has_active_period = $request->has_active_period ?? false;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;

        // Handle removal of background image
        if ($request->has('remove_background_image') && $request->remove_background_image) {
            if ($campaign->background_image) {
                Storage::disk('public')->delete($campaign->background_image);
            }
            $campaign->background_image = null;
        }

        // Handle background image update
        if ($request->hasFile('background_image')) {
            if ($campaign->background_image) {
                Storage::disk('public')->delete($campaign->background_image);
            }
            $campaign->background_image = $request->file('background_image')->store('campaigns', 'public');
        }

        // Handle removal of campaign banner
        if ($request->has('remove_campaign_banner') && $request->remove_campaign_banner) {
            if ($campaign->campaign_banner) {
                Storage::disk('public')->delete($campaign->campaign_banner);
            }
            $campaign->campaign_banner = null;
        }

        // Handle campaign banner update
        if ($request->hasFile('campaign_banner')) {
            if ($campaign->campaign_banner) {
                Storage::disk('public')->delete($campaign->campaign_banner);
            }
            $campaign->campaign_banner = $request->file('campaign_banner')->store('campaign_banners', 'public');
            $campaign->banner_url = $request->banner_url;
        }

        $campaign->save();

        toastr()->success('Campaign Updated Successfully!');
        return redirect()->route('campaigns');
    }

    public function delete(Campaign $campaign)
    {
        // Delete background image if exists
        if ($campaign->background_image) {
            Storage::disk('public')->delete($campaign->background_image);
        }

        // Delete campaign banner if exists
        if ($campaign->campaign_banner) {
            Storage::disk('public')->delete($campaign->campaign_banner);
        }

        $campaign->delete();

        return redirect()->route('campaigns')->with('flash_success', 'Campaign deleted successfully.');
    }

    public function products($id)
    {
        $campaign = Campaign::with('products')->findOrFail($id);
        $products = Product::where('status', 1)->get();
        $existing_products = $campaign->products->pluck('id');

        return view('admin.campaign.products', compact('campaign', 'products', 'existing_products'));
    }

    public function productsAction($id, Request $request)
    {
        $campaign = Campaign::with('products')->findOrFail($id);
        $current_products_ids = $campaign->products->pluck('id')->toArray();
        $sync_values = [];

        if ($request->products) {
            foreach ($request->products as $product_id) {
                if (!in_array($product_id, $current_products_ids)) {
                    $product = Product::findOrFail($product_id);
                    $sync_values[$product->id] = ['campaign_price' => $product->price];
                }
            }
        }

        $campaign->products()->syncWithoutDetaching($sync_values);

        return redirect()->route('campaigns.products', $campaign->id)
            ->with('flash_success', 'Products Synced Successfully.');
    }

    public function productDelete(Campaign $campaign, Product $product)
    {
        $campaign->products()->wherePivot('product_id', '=', $product->id)->detach();

        return redirect()->route('campaigns.products', $campaign->id)
            ->with('flash_success', 'Product Removed Successfully.');
    }

    public function updateDiscount(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'product_id' => 'required|exists:products,id',
            'campaign_price' => 'required|numeric|min:0',
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);
        $campaign->products()->updateExistingPivot($request->product_id, ['campaign_price' => $request->campaign_price]);

        return response()->json(["message" => "Product Updated Successfully"]);
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $index => $id) {
            Campaign::where('id', $id)->update(['display_order' => $index + 1]);
        }
        return response()->json(['success' => true, 'message' => 'Order updated successfully!']);
    }
}

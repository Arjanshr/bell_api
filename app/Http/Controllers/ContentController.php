<?php

namespace App\Http\Controllers;

use App\Models\FeaturedProduct;
use App\Models\NewArraival;
use App\Models\PopularProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index($content_type)
    {
        if ($content_type == 'featured-products') {
            $products = FeaturedProduct::get();
        } elseif ($content_type == 'popular-products') {
            $products = PopularProduct::get();
        } elseif ($content_type == 'new-arriavals') {
            $products = NewArraival::get();
        } else {
            $products = [];
        }
        $all_products = Product::where('status', 'publish')->get();
        return view('admin.content.index', compact('products', 'content_type', 'all_products'));
    }

    public function insert($content_type, Request $request)
    {
        if (isset($request->product_ids) && count($request->product_ids) > 0) {
            foreach ($request->product_ids as $product_id) {
                if ($content_type == 'featured-products') {
                    FeaturedProduct::create(['product_id' => $product_id]);
                } elseif ($content_type == 'popular-products') {
                    PopularProduct::create(['product_id' => $product_id]);
                } elseif ($content_type == 'new-arriavals') {
                    NewArraival::create(['product_id' => $product_id]);
                }
            }
        }
        toastr()->success('Products Added Successfully!');
        return redirect()->route('contents', $content_type);
    }

    public function delete($content_type, $content_id)
    {
        if ($content_type == 'featured-products') {
            $product = FeaturedProduct::find($content_id);
        } elseif ($content_type == 'popular-products') {
            $product = PopularProduct::find($content_id);
        } elseif ($content_type == 'new-arriavals') {
            $product = NewArraival::find($content_id);
        }
        if (isset($product) && $product->count() > 0) {
            $product->delete();
            toastr()->success('Product Deleted Successfully!');
        }
        return redirect()->route('contents', $content_type);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $bell_brand_id = 2; // CHANGE to Bell's actual brand_id

        $products = Product::where('brand_id', $bell_brand_id)
            ->where('status', 1)
            ->limit(10)
            ->get()
            ->map(function ($product) {

                $media = $product->getFirstMedia();

                return [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'price' => $product->price,

                    'image' => $media
                        ? rtrim(env('BELL_ASSET_URL'), '/')
                            . '/storage/'
                            . $media->id
                            . '/'
                            . $media->file_name
                        : null,
                ];
            });

        return response()->json([
            'success' => true,
            'count'   => $products->count(),
            'data'    => $products,
        ]);
    }
}

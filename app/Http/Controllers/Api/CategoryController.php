<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function categories()
    {
        $categories = Category::where('status', 'active')
            ->where('parent_id', null)
            ->with([
                'products' => function ($query) {
                    $query->where('status', 'publish')->with('brand');
                },
                'children.products' => function ($query) {
                    $query->where('status', 'publish')->with('brand');
                },
                'children' => function ($query) {
                    $query->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get()
            ->filter(function ($category) {
                $category->children = $category->children->filter(function ($subcat) {
                    return $subcat->products->isNotEmpty();
                })->values();
                return $category->products->isNotEmpty() || $category->children->isNotEmpty();
            })
            ->values();

        // Sort brands in products and children.products
        $categories->each(function ($category) {
            // Sort products by brand name
            $category->products = $category->products->sortBy(function ($product) {
                return optional($product->brand)->name;
            })->values();

            // Sort brands in subcategories
            $category->children->each(function ($subcat) {
                $subcat->products = $subcat->products->sortBy(function ($product) {
                    return optional($product->brand)->name;
                })->values();
            });
        });

        return $this->sendResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');
    }


    public function details($slug)
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        return $this->sendResponse(CategoryResource::make($category), 'Category detail retrieved successfully.');
    }
}

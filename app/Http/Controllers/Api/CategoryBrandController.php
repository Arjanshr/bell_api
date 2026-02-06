<?php
namespace App\Http\Controllers\Api;
use App\Http\Resources\CategoryBrandResource;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class CategoryBrandController extends BaseController
{
    public function show($category_slug, $brand_slug)
    {
        $category = Category::where('slug', $category_slug)->first();
        $brand = Brand::where('slug', $brand_slug)->first();

        if (!$category || !$brand) {
            return $this->sendError('Category or Brand not found', 404);
        }

        $pivot = DB::table('category_brand')
            ->where('category_id', $category->id)
            ->where('brand_id', $brand->id)
            ->first();

        if (!$pivot) {
            // fallback with empty description
            return $this->sendResponse([
                'category' => $category->name,
                'brand' => $brand->name,
                'summary' => null,
                'description' => null,
            ], 'No brand-category info found');
        }

        return $this->sendResponse(
            new CategoryBrandResource([
                'category' => $category,
                'brand' => $brand,
                'summary' => $pivot->summary,
                'description' => $pivot->description,
                'meta_title' => $pivot->meta_title,
                'meta_description' => $pivot->meta_description,
            ]),
            'Data fetched successfully'
        );
    }
}

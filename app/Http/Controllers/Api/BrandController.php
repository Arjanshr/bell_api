<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    public function brands()
    {
        $brands =  Brand::orderBy('name')->get();
        return $this->sendResponse(BrandResource::collection($brands), 'Brands retrieved successfully.');
    }
    public function details($slug)
    {
        $brand = Brand::where('slug', $slug)->first();
        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found.'
            ], 404);
        }
        return $this->sendResponse(BrandResource::make($brand), 'Brand detail retrieved successfully.');
    }
}

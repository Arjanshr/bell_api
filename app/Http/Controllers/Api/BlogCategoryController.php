<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryResource;
use App\Http\Resources\BlogResource;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends BaseController
{
    public function index()
    {
        $categories = BlogCategory::with(['blogs' => function($q) {
            $q->published()->orderBy('id', 'desc')->with(['author', 'blogCategory']);
        }])->withCount(['blogs' => function($q) {
            $q->published();
        }])->orderBy('name')->get();

        return $this->sendResponse(BlogCategoryResource::collection($categories), 'Blog categories with blogs retrieved successfully.');
    }
}

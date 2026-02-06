<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogDetailResource;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\SlugRedirect;
use Illuminate\Http\Request;

class BlogController extends BaseController
{
    public function blogs()
    {
        $blogs =  Blog::with(['author', 'blogCategory'])->published()->orderBy('id', 'DESC')->get();
        return $this->sendResponse(BlogResource::collection($blogs), 'Blogs retrieved successfully.');
    }

    public function blogDetails($blog_slug)
    {
        $blog = Blog::with(['author', 'blogCategory'])->published()->where('slug', $blog_slug)->first();
        if ($blog)
            return $this->sendResponse(BlogDetailResource::make($blog), 'Blog details retrieved successfully.');

        // Check for slug redirect
        $finalSlug = SlugRedirect::resolveFinalSlug($blog_slug);
        if ($finalSlug && $finalSlug !== $blog_slug) {
            return response()->json([
                'success' => false,
                'redirect' => true,
                'new_slug' => $finalSlug,
                'message' => 'Blog has moved permanently.',
            ], 301);
        }

        return response()->json([
            'success' => false,
            'message' => 'Blog not found.'
        ], 404);
    }

    public function blogsByAuthor($authorId)
    {
        $blogs = Blog::with(['author', 'blogCategory'])
            ->published()
            ->where('author_id', $authorId)
            ->orderBy('id', 'DESC')
            ->get();

        return $this->sendResponse(BlogResource::collection($blogs), 'Blogs by author retrieved successfully.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\GalleryImageResource;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->get('per_page', 12);
        $galleries = Gallery::where('status', true)->orderBy('created_at', 'desc')->paginate($per_page);
        return GalleryResource::collection($galleries);
    }

    public function show($slug)
    {
        $gallery = Gallery::where('slug', $slug)->where('status', true)->with('images')->firstOrFail();
        return new GalleryResource($gallery);
    }

    public function images($slug)
    {
        $gallery = Gallery::where('slug', $slug)->where('status', true)->with('images')->firstOrFail();
        return GalleryImageResource::collection($gallery->images);
    }
}

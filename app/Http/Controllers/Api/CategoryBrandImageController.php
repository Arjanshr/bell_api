<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryBrandImageResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryBrandImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:2048'
        ]);

        $file = $request->file('upload');
        $filename = uniqid('catbrand_') . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/category-brand', $filename);

        return new CategoryBrandImageResource([
            'fileName' => $filename,
            'url' => Storage::url('category-brand/' . $filename),
        ]);
    }
}

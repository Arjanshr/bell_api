<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CKeditorController extends Controller
{
    public function blogImageUpload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $folder = 'public/ckeditor/blogs';

            // Ensure folder exists and has correct permissions
            $folderPath = storage_path('app/' . $folder);
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true); // Creates directory with read access
            }

            // Store file
            $file->storeAs($folder, $filename);

            $url = asset('storage/ckeditor/blogs/' . $filename);

            return response()->json([
                'url' => $url,
                'uploaded' => true,
                'default' => $url
            ]);
        }

        return response()->json([
            'error' => [
                'message' => 'No file uploaded.'
            ]
        ], 400);
    }

    public function productImageUpload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $folder = 'public/ckeditor/products';

            $folderPath = storage_path('app/' . $folder);
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }

            $file->storeAs($folder, $filename);

            $url = asset('storage/ckeditor/products/' . $filename);

            return response()->json([
                'url' => $url,
                'uploaded' => true,
                'default' => $url
            ]);
        }

        return response()->json([
            'error' => [
                'message' => 'No file uploaded.'
            ]
        ], 400);
    }

    public function categoryBrandImageUpload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = uniqid('catbrand_') . '.' . $file->getClientOriginalExtension();
            $folder = public_path('uploads/category-brand');

            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $file->move($folder, $filename);

            $url = asset('uploads/category-brand/' . $filename);

            return response()->json([
                'url' => $url,
                'uploaded' => true,
                'default' => $url
            ]);
        }

        return response()->json([
            'error' => [
                'message' => 'No file uploaded.'
            ]
        ], 400);
    }
}

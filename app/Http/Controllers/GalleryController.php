<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Http\Requests\UploadGalleryImagesRequest;
use App\Http\Requests\UpdateGalleryImageRequest;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $per_page = $request->get('per_page', 15);

        $query = Gallery::query();
        if ($search) {
            $query->where('title', 'like', "%{$search}%")->orWhere('type', 'like', "%{$search}%");
        }

        $galleries = $query->orderBy('created_at', 'desc')->paginate($per_page);
        return view('admin.galleries.index', compact('galleries', 'search'));
    }

    public function create()
    {
        return view('admin.galleries.create');
    }

    public function store(StoreGalleryRequest $request)
    {
        $data = $request->validated();

        $slug = Str::slug($data['title']);
        if (Gallery::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        $gallery = Gallery::create([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'status' => $request->boolean('status', true),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gallery', 'public');
                $max = GalleryImage::where('gallery_id', $gallery->id)->max('sort_order') ?? 0;
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $path,
                    'caption' => null,
                    'sort_order' => $max + 1,
                ]);
            }
        }

        return redirect()->route('galleries')->with('success', 'Gallery created successfully.');
    }

    public function edit(Gallery $gallery)
    {
        return view('admin.galleries.edit', compact('gallery'));
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery)
    {
        $data = $request->validated();

        $gallery->update([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('galleries')->with('success', 'Gallery updated successfully.');
    }

    public function destroy(Gallery $gallery)
    {
        $gallery->delete();
        return back()->with('success', 'Gallery deleted.');
    }

    public function images(Gallery $gallery)
    {
        $gallery->load('images');
        return view('admin.galleries.images', compact('gallery'));
    }

    public function uploadImages(UploadGalleryImagesRequest $request, Gallery $gallery)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('gallery', 'public');
                $max = GalleryImage::where('gallery_id', $gallery->id)->max('sort_order') ?? 0;
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => $path,
                    'caption' => null,
                    'sort_order' => $max + 1,
                ]);
            }
        }

        return redirect()->route('gallery.images', $gallery->id)->with('success', 'Images uploaded.');
    }

    public function updateImage(UpdateGalleryImageRequest $request, GalleryImage $gallery_image)
    {
        $data = $request->validated();

        $gallery_image->caption = $data['caption'] ?? null;
        $gallery_image->save();

        return response()->json(['success' => true]);
    }

    public function deleteImage(GalleryImage $gallery_image)
    {
        Storage::disk('public')->delete($gallery_image->image_path);
        $gallery_image->delete();
        return back()->with('success', 'Image removed.');
    }

    public function reorderImages(Request $request, Gallery $gallery)
    {
        $order = $request->get('order', []); // expected: [image_id => sort_order]
        foreach ($order as $id => $sort) {
            GalleryImage::where('id', $id)->where('gallery_id', $gallery->id)->update(['sort_order' => (int)$sort]);
        }
        return response()->json(['success' => true]);
    }
}

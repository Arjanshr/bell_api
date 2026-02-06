<?php

namespace App\Http\Controllers;

use App\Enums\BrandType;
use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('blogCategory')->paginate(100);
        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.form', compact('categories'));
    }

    public function insert(BlogRequest $request)
    {
        $blog = $request->validated();
        $blog['image'] = $request->hasFile('image') ? $request->validated()['image']->file_name : null;
        $blog['image_alt'] = $request->image_alt;
        $blog['blog_category_id'] = $request->blog_category_id;
        $blog['author_id'] = Auth::id(); // Set the current user as author
        $blog['meta_title'] = $request->meta_title;
        $blog['meta_description'] = $request->meta_description;
        Blog::create($blog);
        toastr()->success('Blog Created Successfully!');
        return redirect()->route('blogs');
    }
    // Only keep the correct update method below, remove duplicate and extra closing brace

    public function show(Blog $blog)
    {
        $blog->load('blogCategory');
        return view('admin.blog.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.form', compact('blog', 'categories'));
    }

    public function update(Blog $blog, BlogRequest $request)
    {
        $data = $request->only([
            'title',
            'content',
            'status',
            'blog_category_id',
            'meta_title',
            'meta_description',
            'image_alt',
            'slug',
        ]);

        if ($request->hasFile('image')) {
            if (File::exists(storage_path("app/public/blogs/$blog->image"))) {
                File::delete(storage_path("app/public/blogs/$blog->image"));
            }
            $data['image'] = $request->validated()['image']->file_name;
        }
        $blog->fill($data);
        $blog->save();
        toastr()->success('Blog Edited Successfully!');
        return redirect()->route('blogs');
    }

    public function delete(Blog $blog)
    {
        if (File::exists(storage_path("app/public/blogs/$blog->image")))
            File::delete(storage_path("app/public/blogs/$blog->image"));
        $blog->delete();
        toastr()->success('Blog Deleted Successfully!');
        return redirect()->route('blogs');
    }

    public function export(Request $request)
    {
        $blogs = Blog::with('blogCategory')->orderByDesc('id')->get();

        $csv_data = [];
        $csv_data[] = ['Blog URL', 'Blog Title', 'Category', 'Image Links', 'Status', 'Published date','Last Modified'];

        foreach ($blogs as $blog) {
            // Blog URL
            $blog_url = "https://mobilemandu.com/blogs/{$blog->slug}";

            // Image Links (if you support multiple images, adjust accordingly)
            $image_links = '';
            if (method_exists($blog, 'getMedia')) {
                // If using spatie/laravel-medialibrary or similar
                $image_links = $blog->getMedia()->map(fn($media) => $media->getUrl())->implode(', ');
            } elseif (!empty($blog->image)) {
                // Fallback: single image field
                $image_links = asset('storage/blogs/' . $blog->image);
            } else {
                $image_links = 'N/A';
            }

            $csv_data[] = [
                $blog_url,
                $blog->title,
                $blog->blogCategory ? $blog->blogCategory->name : '-',
                $image_links,
                $blog->status,
                $blog->created_at ? $blog->created_at->format('Y-m-d H:i:s') : '',
                $blog->updated_at ? $blog->updated_at->format('Y-m-d H:i:s') : ''
            ];
        }

        $filename = 'blogs_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'w+');
        foreach ($csv_data as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);

        return Response::stream(function () use ($handle) {
            fpassthru($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}

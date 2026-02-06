<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::orderBy('id', 'desc')->paginate(50);
        return view('admin.blog_category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog_category.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
        ]);
        BlogCategory::create($validated);
        toastr()->success('Blog Category Created Successfully!');
        return redirect()->route('blog-categories.index');
    }

    public function edit(BlogCategory $blogCategory)
    {
        return view('admin.blog_category.form', compact('blogCategory'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('blog_categories', 'name')->ignore($blogCategory->id),
            ],
        ]);
        $blogCategory->update($validated);
        toastr()->success('Blog Category Updated Successfully!');
        return redirect()->route('blog-categories.index');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();
        toastr()->success('Blog Category Deleted Successfully!');
        return redirect()->route('blog-categories.index');
    }
}

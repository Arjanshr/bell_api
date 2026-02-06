<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CategoryBrandRequest;

class CategoryBrandController extends Controller
{
    public function index()
    {
        $relations = Category::join('category_brand', 'categories.id', '=', 'category_brand.category_id')
            ->join('brands', 'brands.id', '=', 'category_brand.brand_id')
            ->select(
                'category_brand.*',
                'categories.name as category_name',
                'brands.name as brand_name'
            )
            ->orderBy('categories.name')
            ->orderBy('brands.name')
            ->get();

        return view('admin.category_brand.index', compact('relations'));
    }

    public function create()
    {
        // Get only last child categories that have products
        $categories = Category::whereDoesntHave('children')
            ->whereHas('products')
            ->with('parent')
            ->get()
            ->map(function ($cat) {
                if ($cat->parent) {
                    $cat->display_name = $cat->parent->name . ' → ' . $cat->name;
                } else {
                    $cat->display_name = $cat->name;
                }
                return $cat;
            });

        $brands = Brand::all();
        $mode = 'create';
        return view('admin.category_brand.form', compact('categories', 'brands', 'mode'));
    }

    public function store(CategoryBrandRequest $request)
    {
        $exists = Category::find($request->category_id)
            ->brands()
            ->where('brands.id', $request->brand_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['This category-brand relation already exists.']);
        }

        $category = Category::findOrFail($request->category_id);
        $category->brands()->attach($request->brand_id, [
            'summary' => $request->input('summary'),
            'description' => $request->input('description'),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('category-brand.index')->with('success', 'Category-Brand relation created successfully.');
    }

    public function edit($categoryId, $brandId)
    {
        $category = Category::findOrFail($categoryId);
        $brand = Brand::findOrFail($brandId);
        $pivot = $category->brands()->where('brands.id', $brandId)->first()?->pivot;
        $mode = 'edit';
        return view('admin.category_brand.form', compact('category', 'brand', 'pivot', 'mode'));
    }

    public function update(CategoryBrandRequest $request, $categoryId, $brandId)
    {
        $category = Category::findOrFail($categoryId);
        $category->brands()->updateExistingPivot($brandId, [
            'summary' => $request->input('summary'),
            'description' => $request->input('description'),
            'meta_title' => $request->input('meta_title'),
            'meta_description' => $request->input('meta_description'),
            'updated_at' => now(),
        ]);

        return redirect()->route('category-brand.edit', [$categoryId, $brandId])
            ->with('success', 'Summary and description updated successfully.');
    }

    public function delete($categoryId, $brandId)
    {
        $category = Category::findOrFail($categoryId);
        $category->brands()->detach($brandId);

        return redirect()->route('categories')->with('success', 'Category-Brand relation deleted successfully.');
    }
}

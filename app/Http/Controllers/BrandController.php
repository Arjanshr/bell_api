<?php

namespace App\Http\Controllers;

use App\Enums\BrandType;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::paginate(100);
        return view('admin.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.form');
    }

    public function insert(BrandRequest $request)
    {
        $brand = $request->validated();
        $brand['image'] = $request->hasFile('image') ? $request->validated()['image']->file_name : null;
        // Add summary field
        $brand['summary'] = $request->input('summary');
        Brand::create($brand);
        toastr()->success('Brand Created Successfully!');
        return redirect()->route('brands');
    }

    public function show(Brand $brand)
    {
        return view('admin.brand.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brand.form', compact('brand'));
    }

    public function update(Brand $brand, BrandRequest $request)
    {
        $brand->name = $request->name;
        $brand->description = $request->description;
        $brand->summary = $request->input('summary');
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        if ($request->hasFile('image')) {
            if (File::exists(storage_path("app/public/brands/$brand->image")))
                File::delete(storage_path("app/public/brands/$brand->image"));
            $brand->image = $request->validated()['image']->file_name;
        }
        $brand->save();
        toastr()->success('Brand Edited Successfully!');
        return redirect()->route('brands');
    }

    public function delete(Brand $brand)
    {
        if ($brand->checkIfHasItems())
            return redirect()->route('brands')
                ->withError('Brand cannot be deleted!')
                ->withWarning('Delete all the items of this brand first');
        if (File::exists(storage_path("app/public/brands/$brand->image")))
            File::delete(storage_path("app/public/brands/$brand->image"));
        $brand->delete();
        toastr()->success('Brand Deleted Successfully!');
        return redirect()->route('brands');
    }
}

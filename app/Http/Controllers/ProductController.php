<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\SlugRedirect;
use App\Models\Specification;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    // region: Product CRUD

    public function index(Request $request)
    {
        $selected_brand = $request->brand_id ?? null;
        $selected_categories = $request->category_id ?? [];
        $search_query = $request->get('query') ?? null;
        $items_per_page = $request->get('items_per_page', 20);
        $only_unpublished = $request->get('only_unpublished', false);

        $products = $this->oldSearch($request, $search_query, $items_per_page, $only_unpublished);

        return view('admin.product.index', [
            'products' => $products,
            'brands' => Brand::all(),
            'categories' => Category::all(),
            'selected_brand' => $selected_brand,
            'selected_categories' => $selected_categories,
            'query' => $search_query,
        ]);
    }

    public function create()
    {
        return view('admin.product.form');
    }

    public function insert(ProductRequest $request)
    {
        $data = $request->validated();
        unset($data['categories']);
        $product = Product::create($data);
        $product->categories()->sync($request->category_id);
        toastr()->success('Product Created Successfully!');
        return redirect()->route('product.specification.create', $product->id);
    }

    public function show(Product $product)
    {
        return view('admin.product.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.product.form', compact('product'));
    }

    public function update(Product $product, ProductRequest $request)
    {
        $product->update($request->only([
            'name',
            'brand_id',
            'description',
            'short_description',
            'price',
            'warranty',
            'status',
            'in_stock',
            'alt_text',
            'keywords',
            'slug',
        ]));

        $product->categories()->sync($request->category_id);

        toastr()->success('Product Edited Successfully!');

        $action = $request->input('action', 'save');
        if ($action === 'exit') {
            $redirectUrl = $request->input('redirect_url');
            if ($redirectUrl) {
                return redirect($redirectUrl);
            }
            return redirect()->route('products');
        }
        // Default: stay on edit page
        return redirect()->route('product.edit', $product->id);
    }

    public function delete(Product $product)
    {
        $product->delete();
        toastr()->success('Product Deleted Successfully!');
        return redirect()->route('products');
    }

    // endregion

    // region: Category/Brand/Filter Helpers

    protected function oldSearch(Request $request, $search_query, $paginate, $only_unpublished = false)
    {
        $products = Product::query()->with(['categories', 'brand']);

        $selected_brand = $request->brand_id ?? null;
        if ($selected_brand) {
            $products->where('brand_id', $selected_brand);
        }

        $selected_categories = $request->category_id ?? [];
        if (!empty($selected_categories)) {
            $cat_ids = $this->getAllCategoryIds($selected_categories);
            $products->whereHas('categories', function ($q) use ($cat_ids) {
                $q->whereIn('categories.id', $cat_ids);
            });
        }

        // Support multiple product names separated by |||
        if (!empty($search_query)) {
            if (strpos($search_query, '|||') !== false) {
                $names = array_map('trim', explode('|||', $search_query));
                $products->where(function ($query) use ($names) {
                    foreach ($names as $name) {
                        if ($name !== '') {
                            $query->orWhere('name', $name);
                        }
                    }
                });
            } else {
                $plural_query = \Illuminate\Support\Str::plural($search_query);
                $singular_query = \Illuminate\Support\Str::singular($search_query);

                $category_ids = Category::where('name', 'like', '%' . $search_query . '%')
                    ->orWhere('name', 'like', '%' . $plural_query . '%')
                    ->orWhere('name', 'like', '%' . $singular_query . '%')
                    ->pluck('id');

                $products->where(function ($query) use ($search_query, $plural_query, $singular_query, $category_ids) {
                    $query->whereHas('categories', function ($q) use ($category_ids) {
                        $q->whereIn('categories.id', $category_ids);
                    })
                        ->orWhere('name', 'like', '%' . $search_query . '%')
                        ->orWhere('name', 'like', '%' . $plural_query . '%')
                        ->orWhere('name', 'like', '%' . $singular_query . '%')
                        ->orWhere('keywords', 'like', '%' . $search_query . '%')
                        ->orWhere('keywords', 'like', '%' . $plural_query . '%')
                        ->orWhere('keywords', 'like', '%' . $singular_query . '%');
                });

                $temp_query = clone $products;
                if ($temp_query->count() <= 5) {
                    $products->orWhere('description', 'like', '%' . $search_query . '%')
                        ->orWhere('description', 'like', '%' . $plural_query . '%')
                        ->orWhere('description', 'like', '%' . $singular_query . '%');
                }
            }
        }

        if ($only_unpublished) {
            $products->where('status', '!=', 'publish')->where('status', '!=', 'active');
        }

        return $products->orderBy('id', 'DESC')->paginate($paginate);
    }

    protected function getAllCategoryIds(array $selected_categories)
    {
        $all_category_ids = [];
        $categories = Category::whereIn('id', $selected_categories)->with('children')->get();
        foreach ($categories as $category) {
            $all_category_ids[] = $category->id;
            $all_category_ids = array_merge($all_category_ids, $category->getAllChildrenIds()->toArray());
        }
        return $all_category_ids;
    }

    // endregion

    // region: Import/Export

    public function import(Request $request)
    {
        Excel::queueImport(new ProductsImport, $request->file('import_file'));
        toastr()->success('Product Imported Successfully!');
        return redirect()->route('products');
    }

    public function export(Request $request)
    {
        $selected_brand = $request->brand_id ?? null;
        $selected_categories = $request->category_id ?? [];
        $query = $request->get('query') ?? null;

        $products = Product::with(['categories', 'brand', 'variants', 'media'])
            ->when($selected_brand, fn($query_builder) => $query_builder->where('brand_id', $selected_brand))
            ->when(!empty($selected_categories), fn($query_builder) => $query_builder->whereHas('categories', function ($q) use ($selected_categories) {
                $q->whereIn('categories.id', $this->getAllCategoryIds($selected_categories));
            }))
            // Fix: support multi-name search with ||| and normal search
            ->when($query, function ($query_builder, $query) {
                if (strpos($query, '|||') !== false) {
                    $names = array_map('trim', explode('|||', $query));
                    $query_builder->where(function ($q) use ($names) {
                        foreach ($names as $name) {
                            if ($name !== '') {
                                $q->orWhere('name', $name);
                            }
                        }
                    });
                } else {
                    $plural_query = \Illuminate\Support\Str::plural($query);
                    $singular_query = \Illuminate\Support\Str::singular($query);

                    $category_ids = Category::where('name', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $plural_query . '%')
                        ->orWhere('name', 'like', '%' . $singular_query . '%')
                        ->pluck('id');

                    $query_builder->where(function ($q) use ($query, $plural_query, $singular_query, $category_ids) {
                        $q->whereHas('categories', function ($catQ) use ($category_ids) {
                            $catQ->whereIn('categories.id', $category_ids);
                        })
                        ->orWhere('name', 'like', '%' . $query . '%')
                        ->orWhere('name', 'like', '%' . $plural_query . '%')
                        ->orWhere('name', 'like', '%' . $singular_query . '%')
                        ->orWhere('keywords', 'like', '%' . $query . '%')
                        ->orWhere('keywords', 'like', '%' . $plural_query . '%')
                        ->orWhere('keywords', 'like', '%' . $singular_query . '%');
                    });

                    // If few results, also search in description
                    $temp_query = clone $query_builder;
                    if ($temp_query->count() <= 5) {
                        $query_builder->orWhere('description', 'like', '%' . $query . '%')
                            ->orWhere('description', 'like', '%' . $plural_query . '%')
                            ->orWhere('description', 'like', '%' . $singular_query . '%');
                    }
                }
            })
            ->orderByDesc('id')
            ->get();

        $csv_data = [];
        $csv_data[] = [
            'ID',
            'Title',
            'Description',
            'Availability',
            'Price',
            'Category',
            'Brand',
            'SKU',
            'Condition',
            'Status',
            'Image_Link',
            'Link'
        ];

        foreach ($products as $product) {
            $categories = $product->categories->pluck('name')->implode(', ');
            $brand = $product->brand ? $product->brand->name : 'N/A';
            $variants = $product->variants->pluck('name')->implode(', ');
            // Use getFirstMediaUrl for the first image URL (recommended by Spatie)
            $image_link = method_exists($product, 'getFirstMediaUrl') ? $product->getFirstMediaUrl('default') : 'N/A';
            $image_link = $image_link ?: 'N/A';
            $product_link = "https://mobilemandu.com/products/" . $product->slug;

            $csv_data[] = [
                $product->id,
                $product->name,
                strip_tags($product->description),
                $product->in_stock ? 'in stock' : 'out of stock', // Availability
                $product->price,
                $categories,
                $brand,
                $product->sku ?? 'N/A',
                'New', // Condition
                ucfirst($product->status) . 'ed',
                $image_link,
                $product_link
            ];
        }

        $filename = 'products_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
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

    // endregion

    // region: Specifications

    public function createSpecifications(Product $product)
    {
        $specifications = $product->categories()
            ->first()
            ->specifications()
            ->withPivot('display_order')
            ->orderBy('category_specification.display_order')
            ->get();

        $product_specifications = [];
        foreach ($product->specifications()->get() as $p_spec) {
            $product_specifications[$p_spec->pivot->specification_id] = $p_spec->pivot->value;
        }

        return view('admin.product.specifications-form', compact('product', 'specifications', 'product_specifications'));
    }

    public function insertSpecifications(Product $product, Request $request)
    {
        if ($request->input('action') === 'go_next') {
            return redirect()->route('product.feature.create', $product->id);
        }

        $action = $request->input('action', 'next');
        $validated = $request->validate([
            'value' => 'required|array',
        ]);
        foreach ($request->value as $specification_id => $value) {
            if ($value != null && $value != '') {
                $product_specification = ProductSpecification::where('product_id', $product->id)
                    ->where('specification_id', $specification_id)
                    ->first();
                if (!$product_specification)
                    $product_specification = new ProductSpecification();
                $product_specification->product_id = $product->id;
                $product_specification->specification_id = $specification_id;
                $product_specification->value = $value;
                $product_specification->save();
            }
        }
        toastr()->success('Product Created Successfully!');
        if ($action === 'exit') {
            return redirect()->route('product.specifications', $product->id);
        }
        return redirect()->route('product.feature.create', $product->id);
    }

    public function manageSpecifications(Product $product)
    {
        $specifications = $product->categories()
            ->first()
            ->specifications()
            ->withPivot('display_order')
            ->orderBy('category_specification.display_order')
            ->get();
        $product_specifications = [];
        foreach ($specifications as $specification) {
            $specification_data = $product->specifications()->where('specification_id', $specification->id)->first();
            if ($specification_data !== null) {
                $product_specifications[] = $specification_data;
            }
        }
        return view('admin.product.specifications', compact('product_specifications', 'product'));
    }

    public function editSpecifications(ProductSpecification $product_specification)
    {
        return view('admin.product.specifications-form', compact('product_specification'));
    }

    public function updateSpecifications(ProductSpecification $product_specification, Request $request)
    {
        if (isset($request->name) && $request->name != '') {
            $specification = Specification::firstOrCreate([
                'name' =>  $request->name
            ]);
            $product_specification->specification_id = $specification->id;
        }
        $product_specification->value = $request->value;
        $product_specification->save();
        toastr()->success('Product Edited Successfully!');
        return redirect()->route('product.specifications', $product_specification->product->id);
    }

    public function deleteSpecifications(Product $product, Specification $specification)
    {
        $product->specifications()->detach($specification->id);
        toastr()->success('Product Specification Deleted Successfully!');
        return redirect()->route('product.specifications', $product->id);
    }

    public function deleteAllSpecifications(Product $product)
    {
        $product->specifications()->detach();
        toastr()->success('Product Specification Deleted Successfully!');
        return redirect()->route('product.specifications', $product->id);
    }

    // endregion

    // region: Features

    public function createFeatures(Product $product)
    {
        return view('admin.product.features-form', compact('product'));
    }

    public function insertFeatures(Product $product, Request $request)
    {
        if ($request->input('action') === 'go_next') {
            return redirect()->route('product.images', $product->id);
        }

        $request->validate([
            'feature' => 'required|string',
        ]);
        $action = $request->input('action', 'next');
        $feature = new Feature();
        $feature->feature = $request->feature;
        $feature->product_id = $product->id;
        $feature->save();
        toastr()->success('Product Feature Created Successfully!');

        if ($action === 'add_another') {
            return redirect()->route('product.feature.create', $product->id);
        } elseif ($action === 'exit') {
            return redirect()->route('product.features', $product->id);
        }
        return redirect()->route('product.images', $product->id);
    }

    public function manageFeatures(Product $product)
    {
        $product_features = Feature::where('product_id', $product->id)->with('product')->get();
        return view('admin.product.features', compact('product_features', 'product'));
    }

    public function editFeatures(Feature $feature)
    {
        $product = $feature->product;
        return view('admin.product.features-form', compact('feature', 'product'));
    }

    public function updateFeatures(Feature $feature, Request $request)
    {
        $feature->feature = $request->feature;
        $feature->save();
        toastr()->success('Product Feature Edited Successfully!');
        return redirect()->route('product.features', $feature->product->id);
    }

    public function deleteFeatures(Feature $feature)
    {
        $feature->delete();
        toastr()->success('Product Feature Deleted Successfully!');
        return redirect()->route('product.features', $feature->product_id);
    }

    public function deleteAllFeatures(Product $product)
    {
        $product->features()->delete();
        toastr()->success('Product Features Deleted Successfully!');
        return redirect()->route('product.features', $product->id);
    }

    // endregion

    // region: Images

    public function manageImages(Product $product)
    {
        $media = $product->getMedia()->map(function ($item) {
            return [
                'file_name' => $item->file_name,
                'size' => $item->size,
                'uuid' => $item->uuid,
                'original_url' => $item->getUrl(),
                'alt_text' => $item->getCustomProperty('alt_text', ''),
            ];
        });
        return view('admin.product.images', [
            'product' => $product,
            'media' => $media
        ]);
    }

    public function linkImages()
    {
        $products = Product::get();
        foreach ($products as $product) {
            foreach ($product->images as $image) {
                if (file_exists(storage_path('app/public/products/' . $image->image))) {
                    $product
                        ->addMedia(storage_path('app/public/products/' . $image->image))
                        ->toMediaCollection();
                }
            }
        }
        return "Images Linked";
    }

    public function insertImages(Product $product, Request $request)
    {
        $media = $product->addMedia($request->file('image'))
            ->withCustomProperties([
                'alt_text' => $request->input('alt_text', ''),
            ])
            ->toMediaCollection();

        return response()->json([
            'success' => true,
            'id' => $media->uuid,
        ]);
    }

    public function updateImages(Product $product, Request $request)
    {
        // Handle reordering
        if ($request->has('order') && is_array($request->order)) {
            foreach ($request->order as $index => $uuid) {
                $media = $product->getMedia()->where('uuid', $uuid)->first();
                if ($media) {
                    $media->order_column = $index;
                    $media->save();
                }
            }
            return response()->json(['success' => true]);
        }

        // Handle alt_text or single update
        $media = $product->getMedia()->where('file_name', $request->name)->first();
        if (!$media && $request->has('id')) {
            $media = $product->getMedia()->where('uuid', $request->id)->first();
        }
        if (!$media) {
            return response()->json(['error' => 'Media not found'], 404);
        }
        if ($request->has('count')) {
            $media->order_column = $request->count;
        }
        if ($request->has('alt_text')) {
            $media->setCustomProperty('alt_text', $request->input('alt_text', ''));
        }
        $media->save();
        return response()->json(['success' => true]);
    }

    public function deleteImages(Product $product, Request $request)
    {
        $product->getMedia()->where('uuid', $request->id)->first()->delete();
    }

    // endregion

    // region: Variants

    public function createVariants(Product $product)
    {
        $product = Product::with(['categories.specifications' => function ($query) {
            $query->wherePivot('is_variant', true);
        }])->find($product->id);
        $variant_specifications = $product->categories->first()->specifications;
        return view('admin.product.variants-form', compact('product', 'variant_specifications'));
    }

    public function insertVariants(Request $request, Product $product)
    {
        $validated = $request->validate([
            'variants' => 'required|array',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);
        $variants = $request['variants'];
        foreach ($variants as $variant_data) {
            $sku = $this->generateUniqueSku($variant_data, $product);
            $variant = $product->variants()->create([
                'price' => $variant_data['price'],
                'stock_quantity' => $variant_data['stock'],
                'sku' => $sku,
            ]);
            foreach ($variant_data as $key => $value) {
                if ($key !== 'price' && $key !== 'stock_quantity' && $key !== 'sku') {
                    $specification = Specification::where('name', $key)->first();
                    if ($specification) {
                        $variant->variant_options()->create([
                            'specification_id' => $specification->id,
                            'value' => $value,
                        ]);
                    }
                }
            }
        }
        toastr()->success('Variants created successfully.');
        return redirect()->back()->with('success', 'Variants created successfully.');
    }

    public function manageVariants(Product $product)
    {
        $product->load(['variants.variant_options.specification']);
        $variants = $product->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'stock' => $variant->stock_quantity,
                'options' => $variant->variant_options->map(fn($option) => [
                    'specification' => $option->specification->name,
                    'value' => $option->value,
                ])->values(),
            ];
        })->values();
        return view('admin.product.variants', compact('product', 'variants'));
    }

    private function generateUniqueSku($variant_data, $product)
    {
        $sku_parts = [];
        $sku_parts[0] = strtoupper($product->slug);
        foreach ($variant_data as $key => $value) {
            if ($key !== 'price' && $key !== 'stock' && $key !== 'sku') {
                $sku_parts[] = strtoupper($key) . '-' . strtoupper($value);
            }
        }
        if (empty($sku_parts)) {
            throw new \Exception("Failed to generate SKU. Missing required specifications.");
        }
        return implode('-', $sku_parts);
    }

    public function editVariants($product_id, $variant_id)
    {
        $product = Product::findOrFail($product_id);
        $variant = $product->variants()->findOrFail($variant_id);
        return view('admin.product.variants_edit', compact('product', 'variant'));
    }

    public function updateVariants(Request $request, $product_id, $variant_id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);
        $product = Product::findOrFail($product_id);
        $variant = $product->variants()->findOrFail($variant_id);
        $variant->update([
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
        ]);
        toastr()->success('Variants edited successfully.');
        return redirect()->route('product.variants', $product_id)
            ->with('success', 'Variant updated successfully.');
    }

    public function deleteVariants($product_id, $variant_id)
    {
        $product = Product::findOrFail($product_id);
        $variant = $product->variants()->findOrFail($variant_id);
        $variant->delete();
        return redirect()->route('product.variants', $product_id)
            ->with('success', 'Variant deleted successfully.');
    }

    public function deleteAllVariants(Product $product)
    {
        $product->specifications()->detach();
        toastr()->success('Product Specification Deleted Successfully!');
        return redirect()->route('product.specifications', $product->id);
    }

    // endregion
}

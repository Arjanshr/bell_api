<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesForFilterResource;
use App\Http\Resources\PriceAndRatingRangeForFilterResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductFeaturesResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductReviewsResource;
use App\Http\Resources\ProductSpecificationsResource;
use App\Http\Resources\QuestionsAndAnswersResource;
use App\Http\Resources\RelatedProductsResource;
use App\Http\Resources\ReviewsSummaryResource;
use App\Http\Resources\VariantDetailResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Log;
use App\Models\ProductViewLog;
use App\Models\QuestionsAndAnswer;
use App\Models\SearchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Detection\MobileDetect;
use Illuminate\Support\Str;
use WhichBrowser\Parser;
use App\Models\SlugRedirect;
use Illuminate\Support\Facades\Schema;
use Meilisearch\Client;

class ProductController extends BaseController
{
    // Build filter string for Meilisearch
    private function buildFiltersForMeilisearch(Request $request)
    {
        $filters = [];
        if (!empty($request->categories) && is_array($request->categories)) {
            if (count($request->categories) === 1) {
                $filters[] = 'categories = ' . $request->categories[0];
            } else {
                $catFilters = array_map(function ($catId) {
                    return 'categories = ' . $catId;
                }, $request->categories);
                $filters[] = '(' . implode(' OR ', $catFilters) . ')';
            }
        }
        if (!empty($request->brand) && is_array($request->brand)) {
            $filters[] = 'brand_id IN [' . implode(',', $request->brand) . ']';
        }
        if (!empty($request->min_price)) {
            $filters[] = 'price >= ' . $request->min_price;
        }
        if (!empty($request->max_price)) {
            $filters[] = 'price <= ' . $request->max_price;
        }
        if (isset($request->min_rating) || isset($request->max_rating)) {
            $min_rating = $request->min_rating ?? 0;
            $max_rating = $request->max_rating ?? 5;
            $filters[] = "rating >= $min_rating AND rating <= $max_rating";
        }
        return "status = 'publish'" . (empty($filters) ? '' : " AND " . implode(' AND ', $filters));
    }



    public function getProductByBrand($slug, $paginate = 8, Request $request)
    {
        $brand = Brand::where('slug', $slug)->first();
        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found.'
            ], 404);
        }

        $products = Product::where('brand_id', $brand->id)
            ->where('status', 'publish');

        // Category filter
        if (!empty($request->categories) && is_array($request->categories)) {
            $category_ids = $request->categories;
            $products = $products->whereHas('categories', function ($query) use ($category_ids) {
                $query->whereIn('categories.id', $category_ids)
                    ->orWhereIn('categories.slug', $category_ids);
            });
        }

        // Price filters
        if (!empty($request->min_price)) {
            $products = $products->where('price', '>=', $request->min_price);
        }
        if (!empty($request->max_price)) {
            $products = $products->where('price', '<=', $request->max_price);
        }

        // Dynamic sorting
        $sort_by = $request->query('sort_by', 'id');    // default 'id'
        $sort_order = $request->query('sort_order', 'desc'); // default DESC

        if (!in_array($sort_order, ['asc', 'desc'])) $sort_order = 'desc';
        if (!Schema::hasColumn('products', $sort_by)) $sort_by = 'id'; // fallback if column doesn't exist

        $products = $products->orderBy($sort_by, $sort_order)->paginate($paginate);

        return $this->sendResponse(ProductResource::collection($products)->resource, 'Products retrieved successfully.');
    }




    public function getCategoriesForBrandFilter(Brand $brand)
    {
        $products = Product::where('brand_id', $brand->id)
            ->where('status', 'publish')
            ->with('categories')
            ->get();
        $category_ids = [];
        foreach ($products as $product) {
            foreach ($product->categories as $category) {
                $category_ids[] = $category->id;
            }
        }
        $categories = Category::whereIn('id', $category_ids)->get();
        return $this->sendResponse(CategoriesForFilterResource::collection($categories)->resource, 'All available categories for selected brand');
    }

    public function getPriceRangeForBrandFilter(Brand $brand)
    {
        $products = Product::select(DB::raw("MIN(price) AS min_price, MAX(price) AS max_price"))
            ->where('brand_id', $brand->id)
            ->where('status', 'publish')
            ->get();
        return $this->sendResponse(PriceAndRatingRangeForFilterResource::collection($products)->resource, 'Price range for selected brand');
    }

    public function getRatingRangeForBrandFilter(Brand $brand)
    {
        $data = ['min_rating' => 0, 'max_rating' => 5];
        return $this->sendResponse($data, 'Ratinge range for selected brand');
    }

    public function getProductByCategory($slug, $paginate = 8, Request $request)
    {
        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        }

        $cat_ids = $category->getAllChildrenIds()->toArray();
        array_push($cat_ids, $category->id);

        $products = Product::whereHas('categories', function ($q) use ($cat_ids) {
            $q->whereIn('id', $cat_ids);
        })->where('status', 'publish');

        // Brand filter
        if (!empty($request->brands) && is_array($request->brands)) {
            $brandValues = $request->brands;
            $ids = array_filter($brandValues, function ($v) {
                return is_numeric($v);
            });
            $slugs = array_filter($brandValues, function ($v) {
                return !is_numeric($v);
            });

            $products = $products->where(function ($q) use ($ids, $slugs) {
                if (!empty($ids)) {
                    $q->whereIn('brand_id', $ids);
                }
                if (!empty($slugs)) {
                    $q->orWhereHas('brand', function ($qb) use ($slugs) {
                        $qb->whereIn('slug', $slugs);
                    });
                }
            });
        }

        // Price filters
        if (!empty($request->min_price)) {
            $products = $products->where('price', '>=', $request->min_price);
        }
        if (!empty($request->max_price)) {
            $products = $products->where('price', '<=', $request->max_price);
        }

        // Dynamic sorting
        $sort_by = $request->query('sort_by', 'id');
        $sort_order = $request->query('sort_order', 'desc');

        if (!in_array($sort_order, ['asc', 'desc'])) $sort_order = 'desc';
        if (!Schema::hasColumn('products', $sort_by)) $sort_by = 'id';

        $products = $products->orderBy($sort_by, $sort_order)->paginate($paginate);

        return $this->sendResponse(ProductResource::collection($products)->resource, 'Products retrieved successfully.');
    }

    public function getBrandsForCategoryFilter(Category $category)
    {
        $cat_ids = [];
        if ($category)
            $cat_ids = $category->getAllChildrenIds()->toArray();
        array_push($cat_ids, $category->id);
        $products = Product::whereHas('categories', function ($q) use ($cat_ids) {
            $q->whereIn('id', $cat_ids);
        })->where('status', 'publish');
        $brand_ids = $products->select('brand_id')->get()->pluck('brand_id');
        $brands = Brand::whereIn('id', $brand_ids)->get();
        return $this->sendResponse(CategoriesForFilterResource::collection($brands)->resource, 'All available brands for selected category');
    }

    public function getPriceRangeForCategoryFilter(Category $category)
    {
        $products = $category->products()
            ->where('status', 'publish')
            ->select('price')
            ->pluck('price');

        $data['min_price'] = $products->min();
        $data['max_price'] = $products->max();

        return $this->sendResponse($data, 'Price range for selected category');
    }

    public function getRatingRangeForCategoryFilter(Category $category)
    {
        $data = ['min_rating' => 0, 'max_rating' => 5];
        return $this->sendResponse($data, 'Ratinge range for selected category');
    }

    public function getProductByBrandAndCategory($brand, $category, $paginate = 8, Request $request)
    {
        $brand = Brand::where('slug', $brand)->first();
        $category = Category::where('slug', $category)->first();
        if ($brand === null) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found.'
            ], 404);
        };
        if ($category === null) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ], 404);
        };
        $products = $category->products()->where('brand_id', $brand->id)
            ->where('status', 'publish');
        $sort_by = $request->query('sort_by', 'id');
        $sort_order = $request->query('sort_order', 'desc');

        if (!in_array($sort_order, ['asc', 'desc'])) $sort_order = 'desc';
        if (!Schema::hasColumn('products', $sort_by)) $sort_by = 'id';

        $products = $products->orderBy($sort_by, $sort_order)->paginate($paginate);
        return $this->sendResponse(ProductResource::collection($products)->resource, 'Products retrieved successfully.');
    }

    // endregion

    // region: Search

    public function searchProducts(Request $request, $paginate = 8)
    {
        $search_query = trim($request->query('query', ''));
        
        $user_agent = $request->userAgent();

        if (strlen($search_query) >= 3) {
            $user_id = auth()->id();
            $ip = $request->ip();

            $query_base = SearchLog::where(function ($q) use ($user_id, $ip) {
                if ($user_id) {
                    $q->where('user_id', $user_id);
                } else {
                    $q->where('ip_address', $ip);
                }
            });

            $similar_logs = SearchLog::where(function ($q) use ($user_id, $ip) {
                if ($user_id) {
                    $q->where('user_id', $user_id);
                } else {
                    $q->where('ip_address', $ip);
                }
            })
                ->where('searched_at', '>=', now()->subMinutes(10))
                ->get();

            foreach ($similar_logs as $log) {
                similar_text(Str::lower($log->term), Str::lower($search_query), $percent);
                if ($percent > 80 && strlen($log->term) < strlen($search_query)) {
                    $log->delete();
                }
            }

            $parser = new Parser($user_agent);
            $detect = new MobileDetect();

            SearchLog::create([
                'term' => $search_query,
                'user_id' => $user_id,
                'ip_address' => $ip,
                'user_agent' => $user_agent,
                'device_type' => $detect->isTablet() ? 'tablet' : ($detect->isMobile() ? 'mobile' : 'desktop'),
                'platform' => $parser->os->toString() ?? null,
                'browser' => $parser->browser->toString() ?? null,
                'searched_at' => now(),
            ]);
        }

        $meilisearchFilters = $this->buildFiltersForMeilisearch($request);
        if ($search_query === '') {
            $products = Product::where('status', 'publish')
                ->orderBy('id', 'DESC')
                ->paginate($paginate);
            return $this->sendResponse(ProductResource::collection($products)->resource, 'Products retrieved successfully.');
        }

        $sort = $this->buildSortOptions($request);
        $meilisearch_results = $this->performMeilisearch($search_query, $meilisearchFilters, $paginate, $sort);
        $products = $meilisearch_results;

        return $this->sendResponse(ProductResource::collection($products)->resource, 'Products retrieved successfully.');
    }

    protected function buildSortOptions(Request $request): array
    {
        // requested sort field and order
        $sort_by = $request->get('sort_by', 'id'); // default to 'id'
        $sort_order = strtolower($request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        // map public names -> actual indexed attribute names
        $allowed = [
            'price' => 'price',       // numeric
            'name' => 'name',         // string
            'id' => 'id',             // numeric
            'rating' => 'rating',     // numeric (if you index it)
        ];

        if (!array_key_exists($sort_by, $allowed)) {
            return [$allowed['id'] . ':' . $sort_order]; // fallback to 'id'
        }

        return [$allowed[$sort_by] . ':' . $sort_order];
    }


    private function performMeilisearch($search_query, $filter_string, $paginate, $sort = [])
    {
        $meilisearch = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $meilisearch->index('products');

        // Add all attributes you need for filtering
        $index->updateFilterableAttributes([
            'status',
            'brand_id',
            'categories',
            'price', // <-- add price here
        ]);
        $index->updateSortableAttributes([
            'price',
            'id',
            'name',
            'created_at_timestamp',
        ]);
        $final_filter = $filter_string;
        if (strpos($filter_string, "status = 'publish'") === false) {
            $final_filter = empty($filter_string) ? "status = 'publish'" : "status = 'publish' AND " . $filter_string;
        }

        $products = Product::search($search_query, function ($meilisearch, $query, $options) use ($final_filter, $sort) {
            if (!empty($final_filter)) {
                $options['filter'] = $final_filter;
            }

            if (!empty($sort)) {
                $options['sort'] = $sort;
            }

            return $meilisearch->search($query, $options);
        })->paginate($paginate);
        $sort = (explode(':', $sort[0]));
        // Append the search query to pagination links
        $products->appends([
            'query' => $search_query,
            'sort_by' => $sort[0],       // you should define $sort_by above
            'sort_order' => $sort[1], // you should define $sort_order above
        ]);

        return $products;
    }


    // endregion

    // region: Search Filters

    public function getBrandsForSearch(Request $request)
    {
        $products = Product::where('status', 'publish');
        if (isset($request->query) && $request->query != '') {
            $category_ids = Category::where('name', 'like', '%' . $request->get('query') . '%')->pluck('id');
            $products = $products->whereHas('categories', function ($query) use ($category_ids) {
                $query->whereIn('id', $category_ids);
            });
            $products = $products->orWhere(function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->get('query') . '%');
            });
            if ($products->count() <= 0) {
                $products = $products->orWhere('description', 'like', '%' . $request->get('query') . '%');
            }
        }
        $brand_ids = $products->select('brand_id')->get()->pluck('brand_id');
        $brands = Brand::whereIn('id', $brand_ids)->get();
        return $this->sendResponse(CategoriesForFilterResource::collection($brands)->resource, 'All available brands for product list');
    }

    public function getCategoriesForSearch(Request $request)
    {
        $products = Product::where('status', 'publish');
        if (isset($request->query) && $request->query != '') {
            $category_ids = Category::where('name', 'like', '%' . $request->get('query') . '%')->pluck('id');
            $products = $products->whereHas('categories', function ($query) use ($category_ids) {
                $query->whereIn('id', $category_ids);
            });
            $products = $products->orWhere(function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->get('query') . '%');
            });
            if ($products->count() <= 0) {
                $products = $products->orWhere('description', 'like', '%' . $request->get('query') . '%');
            }
        }
        $products = $products->get();
        $category_ids = [];
        foreach ($products as $product) {
            foreach ($product->categories as $category) {
                $category_ids[] = $category->id;
            }
        }
        $categories = Category::whereIn('id', $category_ids)->get();
        return $this->sendResponse(CategoriesForFilterResource::collection($categories)->resource, 'All available categories for product list');
    }

    public function getPriceRangeForSearch(Request $request)
    {
        $products = Product::where('status', 'publish');
        if (isset($request->query) && $request->query != '') {
            $category_ids = Category::where('name', 'like', '%' . $request->get('query') . '%')->pluck('id');
            $products = $products->whereHas('categories', function ($query) use ($category_ids) {
                $query->whereIn('id', $category_ids);
            });
            $products = $products->orWhere(function ($query) use ($request) {
                $query->orWhere('name', 'like', '%' . $request->get('query') . '%');
            });
            if ($products->count() <= 0) {
                $products = $products->orWhere('description', 'like', '%' . $request->get('query') . '%');
            }
        }
        $products = $products->select(DB::raw("MIN(price) AS min_price, MAX(price) AS max_price"))->get();
        return $this->sendResponse(PriceAndRatingRangeForFilterResource::collection($products)->resource, 'Price range for product list');
    }

    public function getRatingRangeForSearch(Request $request)
    {
        $data = ['min_rating' => 0, 'max_rating' => 5];
        return $this->sendResponse($data, 'Ratinge range for product list');
    }

    // endregion

    // region: Product Details/Features/Specs/Reviews

    public function productDetails($slug, Request $request)
    {
        // 1️⃣ Direct match → Published product by slug
        $product = Product::where('status', 'publish')->where('slug', $slug)->first();
        if ($product) {
            return $this->sendResponse(ProductDetailResource::make($product), 'Product detail retrieved successfully.');
        }

        // 2️⃣ Slug contains numeric ID → redirect to correct slug
        // Only match if slug is fully numeric and <= 4 digits
        if (preg_match('/^\d{1,4}$/', $slug)) {
            $id = (int) $slug;
            $productById = Product::where('status', 'publish')->find($id);
            if ($productById) {
                return response()->json([
                    'success' => false,
                    'redirect' => true,
                    'new_slug' => $productById->slug,
                    'message' => 'Product ID detected. Redirecting to correct product.',
                ], 301);
            }
        }


        // 3️⃣ Unpublished product → redirect to category/brand page
        $unpublishedProduct = Product::where('slug', $slug)->first();
        if ($unpublishedProduct) {
            $category = $unpublishedProduct->categories->first();
            $brand = $unpublishedProduct->brand;

            if ($category && $brand) {
                return response()->json([
                    'success' => false,
                    'redirect' => true,
                    'category_slug' => $category->slug,
                    'brand_slug' => $brand->slug,
                    'message' => 'Product discontinued. Redirecting to category & brand page.',
                ], 301);
            }
        }

        // 4️⃣ SlugRedirect chain (legacy slug mappings)
        $finalSlug = SlugRedirect::resolveFinalSlug($slug);
        if ($finalSlug && $finalSlug !== $slug) {
            $redirectProduct = Product::where('status', 'publish')->where('slug', $finalSlug)->first();
            if ($redirectProduct) {
                return response()->json([
                    'success' => false,
                    'redirect' => true,
                    'new_slug' => $finalSlug,
                    'message' => 'Product has moved permanently.',
                ], 301);
            }
        }

        // 5️⃣ MeiliSearch fallback → find similar slug keyword
        try {
            $meilisearch = new \Meilisearch\Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
            $index = $meilisearch->index('products');

            $searchResult = $index->search($slug, [
                'limit' => 1,
                'filter' => "status = 'publish'",
                'attributesToRetrieve' => ['id'],
            ]);

            $hits = $searchResult->toArray()['hits'] ?? [];

            if (!empty($hits[0]['id'])) {
                $foundProduct = Product::find($hits[0]['id']);
                if ($foundProduct) {
                    SlugRedirect::updateOrCreate(
                        ['old_slug' => $slug],
                        ['new_slug' => $foundProduct->slug]
                    );
                    return response()->json([
                        'success' => false,
                        'redirect' => true,
                        'new_slug' => $foundProduct->slug,
                        'message' => 'Product not found. Redirecting to a similar product.',
                    ], 301);
                }
            }
        } catch (\Throwable $e) {
            // If Meilisearch fails, continue to 404
        }

        // 6️⃣ Final fallback → 404
        return response()->json([
            'success' => false,
            'message' => 'Product not found.',
        ], 404);
    }



    public function productViewLog(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $recent = ProductViewLog::where('product_id', $request->product_id)
            ->where(function ($q) use ($request) {
                if (auth()->check()) {
                    $q->where('user_id', auth()->id());
                } else {
                    $q->where('ip_address', $request->ip());
                }
            })
            ->where('viewed_at', '>', now()->subMinutes(10))
            ->exists();

        if (! $recent) {
            ProductViewLog::create([
                'product_id' => $request->product_id,
                'user_id' => auth()->id() ?? null,
                'ip_address' => $request->ip(), // should now be the real browser IP
                'viewed_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Product view logged.']);
    }


    public function productFeatures(Product $product)
    {
        return $this->sendResponse(ProductFeaturesResource::collection($product->features), 'Product features retrieved successfully.');
    }

    public function productSpecifications(Product $product)
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
        return $this->sendResponse(ProductSpecificationsResource::collection($product_specifications), 'Product specifications retrieved successfully.');
    }

    public function productReviews(Product $product)
    {
        $confirmedReviews = $product->reviews()->where('status', 'confirmed')->get();
        return $this->sendResponse(ProductReviewsResource::collection($confirmedReviews), 'Product reviews retrieved successfully.');
    }

    public function productReviewsSummary(Product $product)
    {
        return $this->sendResponse(ReviewsSummaryResource::make($product->reviews), 'Product reviews retrieved successfully.');
    }

    public function relatedProducts(Product $product, $count = 8)
    {
        $category_ids = $product->categories()->pluck('id');
        $related_products = Product::where('status', 'publish')
            ->where('brand_id', $product->brand_id)
            ->whereHas('categories', function ($query) use ($category_ids) {
                $query->whereIn('categories.id', $category_ids);
            })
            ->where('id', '!=', $product->id) // Exclude the current product
            ->get()
            ->take($count);
        if ($related_products->count() <= 5) {
            $related_products = Product::where('status', 'publish')
                ->whereHas('categories', function ($query) use ($category_ids) {
                    $query->whereIn('categories.id', $category_ids);
                })
                ->where('id', '!=', $product->id) // Exclude the current product
                ->get()
                ->take($count);
        }
        return $this->sendResponse(RelatedProductsResource::collection($related_products), 'Related products retrieved successfully.');
    }

    public function questionsAndAnswers(Product $product, $count = 8)
    {
        $q_n_a = QuestionsAndAnswer::where('product_id', $product->id)
            ->orderBy('id', 'desc')
            ->get()
            ->take($count);
        return $this->sendResponse(QuestionsAndAnswersResource::collection($q_n_a), 'Q and A retrieved successfully.');
    }

    public function specCompare(Request $request)
    {
        $product_slugs = $request->product_slug;
        $products = Product::with(['specifications'])->whereIn('slug', $product_slugs)->get();
        $comparison_data = [];
        foreach ($products as $product) {
            foreach ($product->specifications as $specification) {
                if (!isset($comparison_data[$specification->name])) {
                    $comparison_data[$specification->name] = [];
                }
                $comparison_data[$specification->name][$product->slug] = $specification->pivot->value;
            }
        }
        foreach ($comparison_data as $specification_name => &$values) {
            foreach ($product_slugs as $product_slug) {
                $values[$product_slug] = $values[$product_slug] ?? 'N/A';
            }
        }
        return $this->sendResponse($comparison_data, "Comparision data fetched successfully");
    }

    public function getVariantDetails($id)
    {
        $variant = ProductVariant::with(['product', 'variant_options.specification'])->find($id);
        if (!$variant) {
            return $this->sendError('Variant not found.', [], 404);
        }
        return $this->sendResponse(new VariantDetailResource($variant), 'Variant details retrieved successfully.');
    }

    // endregion
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentResource;
use App\Http\Resources\RelevantProductResource;
use App\Models\FeaturedProduct;
use App\Models\NewArraival;
use App\Models\PopularProduct;
use App\Models\Product;
use App\Models\SearchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Meilisearch\Client;

class ContentController extends BaseController
{
    public function getProductList($content_type, $items_per_page = 6)
    {
        switch ($content_type) {
            case 'featured-products':
                $products = FeaturedProduct::whereRelation('product', 'status', 'publish')
                    ->orderByDesc('display_order')
                    ->paginate($items_per_page);
                break;
            case 'popular-products':
                $products = PopularProduct::whereRelation('product', 'status', 'publish')
                    ->orderByDesc('display_order')
                    ->paginate($items_per_page);
                break;
            case 'new-arriavals':
                $products = NewArraival::whereRelation('product', 'status', 'publish')
                    ->orderByDesc('display_order')
                    ->paginate($items_per_page);
                break;
            default:
                $products = Product::where('status', 'publish')
                    ->orderByDesc('id')
                    ->paginate($items_per_page);
        }

        return $this->sendResponse(
            ContentResource::collection($products)->resource,
            ucfirst(str_replace('-', ' ', $content_type)) . ' retrieved successfully.'
        );
    }

    public function relevantProducts(Request $request, $limit = 8)
    {
        $keywords = $this->getSearchKeywords($request);

        if (empty($keywords)) {
            return $this->fallbackProducts($limit);
        }

        // Scout-based fuzzy search
        $scout_results = Product::search(implode(' ', $keywords))
            ->where('status', 'publish')
            ->get();

        if ($scout_results->isEmpty()) {
            return $this->fallbackProducts($limit);
        }

        $scored = $this->getRelevanceScoredProducts($scout_results, $keywords);
        $diverse = $this->limitByCategoryWithFallback($scored, $limit);
        $paginated = $this->paginateResults($diverse, $limit);

        return $this->sendResponse(
            RelevantProductResource::collection($paginated)->resource,
            'Relevant products retrieved successfully.'
        );
    }

    /**
     * Extract keywords from search history and wishlist (if logged in)
     *
     * @param Request $request
     * @return array<int, string>
     */
    private function getSearchKeywords(Request $request): array
    {
        $user_id = auth()->id();
        $ip_address = $request->ip();

        $search_logs_query = SearchLog::query()
            ->select('term')
            ->orderByDesc('searched_at');

        if ($user_id) {
            $search_logs_query->where('user_id', $user_id);
        } elseif ($ip_address) {
            $search_logs_query->where('ip_address', $ip_address);
        } else {
            return [];
        }

        $search_terms = $search_logs_query->limit(20)->pluck('term')->toArray();
        $wishlist_titles = [];

        if ($user_id) {
            $wishlist_titles = Product::whereIn('id', function ($q) use ($user_id) {
                $q->select('product_id')->from('wishlists')->where('user_id', $user_id);
            })->pluck('name')->toArray();
        }

        $combined = array_merge($search_terms, $wishlist_titles);
        return collect(explode(' ', implode(' ', $combined)))
            ->map(fn($word) => Str::lower(trim($word)))
            ->filter(fn($word) => strlen($word) > 2)
            ->unique()
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Assigns relevance score to each product based on keyword matching
     *
     * @param Collection<int, Product> $results
     * @param array<int, string> $keywords
     * @return Collection<int, Product>
     */
    private function getRelevanceScoredProducts(Collection $results, array $keywords): Collection
    {
        return $results->map(function (Product $product) use ($keywords) {
            $score = 0;
            foreach ($keywords as $word) {
                if (stripos($product->name, $word) !== false) $score += 3;
                if (stripos($product->description, $word) !== false) $score += 1;
            }
            $product->relevance_score = $score;
            return $product;
        })->sortByDesc('relevance_score')->values();
    }

    /**
     * Limits result variety by category and applies fallback if needed
     *
     * @param Collection<int, Product> $scored
     * @param int $limit
     * @return Collection<int, Product>
     */
    private function limitByCategoryWithFallback(Collection $scored, int $limit): Collection
    {
        $grouped = $scored->flatMap(function ($product) {
            return $product->categories->map(fn($cat) => ['category_id' => $cat->id, 'product' => $product]);
        })->groupBy('category_id');

        $limited = collect();
        foreach ($grouped as $group) {
            foreach ($group->take(4) as $item) {
                $limited->push($item['product']);
            }
        }

        $limited = $limited->unique('id')->take($limit);

        if ($limited->count() < $limit) {
            $excluded_ids = $limited->pluck('id')->toArray();
            $fallback = Product::whereNotIn('id', $excluded_ids)
                ->inRandomOrder()
                ->take($limit - $limited->count())
                ->get();

            $limited = $limited->concat($fallback)->unique('id')->values();
        }

        return $limited->shuffle()->values();
    }

    /**
     * Paginate a collection of products manually
     *
     * @param Collection<int, Product> $collection
     * @param int $per_page
     * @return LengthAwarePaginator
     */
    private function paginateResults(Collection $collection, int $per_page): LengthAwarePaginator
    {
        $current_page = LengthAwarePaginator::resolveCurrentPage();
        $paged = $collection->forPage($current_page, $per_page)->values();

        return new LengthAwarePaginator(
            $paged,
            $collection->count(),
            $per_page,
            $current_page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    /**
     * Fallback if no results found from keywords or scout
     */
    private function fallbackProducts(int $limit)
    {
        $products = Product::inRandomOrder()->where('status', 'publish')->paginate($limit);
        return $this->sendResponse(
            RelevantProductResource::collection($products)->resource,
            'Relevant products retrieved successfully.'
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Spatie\Activitylog\Models\Activity;
use App\Models\SearchLog;
use App\Models\ProductViewLog;
use Illuminate\Support\Facades\DB;
use App\Services\AnalyticsService;

class DashboardController extends Controller
{
    public function dashboard(AnalyticsService $analytics)
    {
        $user_count = User::count();
        $order_count = Order::count();
        $total_revenue = Order::where('status', 'completed')->sum('grand_total');
        $pending_order_count = Order::where('status', 'pending')->count();
        $recent_orders = Order::latest()->take(5)->get();
        $recent_users = User::latest()->take(5)->get();
        $recent_products = Product::with('variants')->latest()->take(5)->get();
        $activities = Activity::latest()->take(10)->get(); 
        $top_keywords = SearchLog::selectRaw('term, COUNT(*) as count')
            ->groupBy('term')
            ->orderByDesc('count')
            ->take(5)
            ->get()
            ->map(function($row) {
                return ['keyword' => $row->term, 'count' => $row->count];
            });

        // Fetch top 5 viewed products from product_view_logs table
        $top_viewed_products = ProductViewLog::with('product:id,name')
            ->select('product_id', DB::raw('count(*) as count'))
            ->groupBy('product_id')
            ->orderByDesc('count')
            ->take(5)
            ->get()
            ->map(function($item) {
                $name = $item->product->name ?? 'Unknown';
                // Truncate product name if too long
                $max_length = 25;
                if (mb_strlen($name) > $max_length) {
                    $name = mb_substr($name, 0, $max_length - 3) . '...';
                }
                return [
                    'product_name' => $name,
                    'count' => $item->count,
                ];
            });

        // Google Analytics data
        $sessions = $analytics->getSessionsLast7Days();
        $user_trend = $analytics->getUsersTrendLast30Days();
        $ga_views_trend_30 = $analytics->getViewsTrendLast30Days();

        // Fetch 5 most recent reviews
        $recent_reviews = Review::with('user', 'product')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'user_count',
            'order_count',
            'total_revenue',
            'pending_order_count',
            'recent_orders',
            'recent_users',
            'recent_products',
            'activities',
            'top_keywords',
            'top_viewed_products',
            'sessions',
            'user_trend',
            'ga_views_trend_30',
            'recent_reviews'
        ));
    }

    public function allReviews()
    {
        $reviews = Review::with('user', 'product')->latest()->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }
}

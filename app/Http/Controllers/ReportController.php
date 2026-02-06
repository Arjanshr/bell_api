<?php

namespace App\Http\Controllers;

use App\Models\SearchLog;
use App\Models\ProductViewLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function searchReport(Request $request)
    {
        $timeFrame = $request->input('time_frame', 'monthly'); // daily, weekly, monthly
        $type = $request->input('type', 'all'); // guest, user, all
        $groupBy = $request->input('group_by', 'user'); // user or keyword, applies only to userReport

        // Define date limit based on time frame
        $dateLimit = match ($timeFrame) {
            'daily' => Carbon::now()->subDay(),
            'weekly' => Carbon::now()->subWeek(),
            'monthly' => Carbon::now()->subMonth(),
            default => Carbon::now()->subMonth(),
        };

        // Base query for guests with filters applied
        $guestQuery = SearchLog::query()
            ->whereNull('user_id')
            ->where('searched_at', '>=', $dateLimit);

        // Base query for users with filters applied
        $userQuery = SearchLog::query()
            ->whereNotNull('user_id')
            ->where('searched_at', '>=', $dateLimit);

        // Apply type filter to queries
        if ($type === 'guest') {
            // Only guests
            $guestReport = $guestQuery
                ->select('term', DB::raw('count(*) as count'))
                ->groupBy('term')
                ->orderByDesc('count')
                ->get();

            $userReport = collect(); // empty collection

        } elseif ($type === 'user') {
            // Only users

            if ($groupBy === 'keyword') {
                // Group user report by term (keyword) only
                $userReport = $userQuery
                    ->select('term', DB::raw('count(*) as count'))
                    ->groupBy('term')
                    ->orderByDesc('count')
                    ->get();
            } else {
                // Group user report by user and term
                $userReport = $userQuery
                    ->select('user_id', 'term', DB::raw('count(*) as count'))
                    ->groupBy('user_id', 'term')
                    ->with('user:id,name')
                    ->get()
                    ->map(function ($item) {
                        return (object)[
                            'user_name' => $item->user->name ?? 'Unknown',
                            'term' => $item->term,
                            'count' => $item->count,
                        ];
                    });
            }

            $guestReport = collect(); // empty collection

        } else {
            // type === 'all', include both guests and users

            $guestReport = $guestQuery
                ->select('term', DB::raw('count(*) as count'))
                ->groupBy('term')
                ->orderByDesc('count')
                ->get();

            if ($groupBy === 'keyword') {
                $userReport = $userQuery
                    ->select('term', DB::raw('count(*) as count'))
                    ->groupBy('term')
                    ->orderByDesc('count')
                    ->get();
            } else {
                $userReport = $userQuery
                    ->select('user_id', 'term', DB::raw('count(*) as count'))
                    ->groupBy('user_id', 'term')
                    ->with('user:id,name')
                    ->get()
                    ->map(function ($item) {
                        return (object)[
                            'user_name' => $item->user->name ?? 'Unknown',
                            'term' => $item->term,
                            'count' => $item->count,
                        ];
                    });
            }
        }

        return view('admin.report.search_report', compact('guestReport', 'userReport', 'timeFrame', 'type', 'groupBy'));
    }

    public function productViewReport(Request $request)
    {
        $timeFrame = $request->input('time_frame', 'monthly'); // daily, weekly, monthly
        $type = $request->input('type', 'all'); // guest, user, all
        $groupBy = $request->input('group_by', 'user'); // user or product

        $dateLimit = match ($timeFrame) {
            'daily' => Carbon::now()->subDay(),
            'weekly' => Carbon::now()->subWeek(),
            'monthly' => Carbon::now()->subMonth(),
            default => Carbon::now()->subMonth(),
        };

        $guestQuery = ProductViewLog::query()
            ->whereNull('user_id')
            ->where('viewed_at', '>=', $dateLimit);

        $userQuery = ProductViewLog::query()
            ->whereNotNull('user_id')
            ->where('viewed_at', '>=', $dateLimit);

        if ($type === 'guest') {
            $guestReport = $guestQuery
                ->select('product_id', DB::raw('count(*) as count'))
                ->groupBy('product_id')
                ->with('product:id,name')
                ->orderByDesc('count')
                ->get()
                ->map(function ($item) {
                    return (object)[
                        'product_name' => $item->product->name ?? 'Unknown',
                        'count' => $item->count,
                    ];
                });
            $userReport = collect();
        } elseif ($type === 'user') {
            if ($groupBy === 'product') {
                $userReport = $userQuery
                    ->select('product_id', DB::raw('count(*) as count'))
                    ->groupBy('product_id')
                    ->with('product:id,name')
                    ->orderByDesc('count')
                    ->get()
                    ->map(function ($item) {
                        return (object)[
                            'product_name' => $item->product->name ?? 'Unknown',
                            'count' => $item->count,
                        ];
                    });
            } else {
                $userReport = $userQuery
                    ->select('user_id', 'product_id', DB::raw('count(*) as count'))
                    ->groupBy('user_id', 'product_id')
                    ->with(['user:id,name', 'product:id,name'])
                    ->get()
                    ->map(function ($item) {
                        return (object)[
                            'user_name' => $item->user->name ?? 'Unknown',
                            'product_name' => $item->product->name ?? 'Unknown',
                            'count' => $item->count,
                        ];
                    });
            }
            $guestReport = collect();
        } else {
            $guestReport = $guestQuery
                ->select('product_id', DB::raw('count(*) as count'))
                ->groupBy('product_id')
                ->with('product:id,name')
                ->orderByDesc('count')
                ->get()
                ->map(function ($item) {
                    return (object)[
                        'product_name' => $item->product->name ?? 'Unknown',
                        'count' => $item->count,
                    ];
                });

            if ($groupBy === 'product') {
                $userReport = $userQuery
                    ->select('product_id', DB::raw('count(*) as count'))
                    ->groupBy('product_id')
                    ->with('product:id,name')
                    ->orderByDesc('count')
                    ->get()
                    ->map(function ($item) {
                        return (object)[
                            'product_name' => $item->product->name ?? 'Unknown',
                            'count' => $item->count,
                        ];
                    });
            } else {
                $userReport = $userQuery
                    ->select('user_id', 'product_id', DB::raw('count(*) as count'))
                    ->groupBy('user_id', 'product_id')
                    ->with(['user:id,name', 'product:id,name'])
                    ->get()
                    ->map(function ($item) {
                        return (object)[
                            'user_name' => $item->user->name ?? 'Unknown',
                            'product_name' => $item->product->name ?? 'Unknown',
                            'count' => $item->count,
                        ];
                    });
            }
        }

        return view('admin.report.product_view_report', compact('guestReport', 'userReport', 'timeFrame', 'type', 'groupBy'));
    }
}

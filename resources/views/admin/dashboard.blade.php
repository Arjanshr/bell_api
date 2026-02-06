@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <div class="container-fluid dashboard-bg">

        <div class="row mb-4">
            <div class="col-lg-12 col-12 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title mb-0">Website Visits & Active Users (Last 30 Days)</h3>
                        <div>
                            <button id="showDualAxisBtn" class="btn btn-sm btn-outline-primary active" type="button">Dual Axis</button>
                            <button id="showComboBtn" class="btn btn-sm btn-outline-primary" type="button">Combo</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="dualAxisChartContainer">
                            <canvas id="visitsActiveUsersChart" height="100"></canvas>
                        </div>
                        <div id="comboChartContainer" style="display:none;">
                            <canvas id="visitsActiveUsersComboChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <!-- Top Searched Keywords Chart -->
            <div class="col-lg-6 mb-3">
                <div class="card dashboard-card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Top Searched Keywords</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="topKeywordsChart" height="180"></canvas>
                        @can('read-reports')
                            <div class="text-center mt-2">
                                <a href="{{ route('search-report') }}" class="btn btn-sm btn-primary">
                                    View All <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
            <!-- Top Viewed Products Chart -->
            <div class="col-lg-6 mb-3">
                <div class="card dashboard-card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Top Viewed Products</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="topProductsChart" height="180"></canvas>
                        @can('read-reports')
                            <div class="text-center mt-2">
                                <a href="{{ route('product-view-report') }}" class="btn btn-sm btn-primary">
                                    View All <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Statistics Widgets -->
            <div class="col-lg-3 col-6 mb-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $pending_order_count }}</h3>
                        <p>Pending Orders</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('orders') }}" class="small-box-footer">
                        Manage Orders <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $user_count }}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('users') }}" class="small-box-footer">
                        Manage Users <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $order_count }}</h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="{{ route('orders') }}" class="small-box-footer">
                        Manage Orders <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-6 mb-3">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>Rs{{ number_format($total_revenue, 2) }}</h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">            
            <!-- Recent Reviews -->
            <div class="col-lg-7 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Reviews</h3>
                        <div class="float-right">
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-primary">
                                View All <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @forelse ($recent_reviews as $review)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>
                                            {{ $review->user ? $review->user->name : 'Anonymous' }}
                                        </strong>
                                        on
                                        <a href="{{ route('product.show', $review->product_id) }}">
                                            {{ $review->product ? $review->product->name : 'Product' }}
                                        </a>
                                        <br>
                                        <span>
                                            Rating:
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                        </span>
                                        <br>
                                        <span>{{ Str::limit($review->review, 80) }}</span>
                                        <br>
                                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                        <br>
                                        <span class="badge badge-{{ $review->status == 'confirmed' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($review->status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <form method="POST" action="{{ route('admin.review.toggle-status', $review->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $review->status == 'confirmed' ? 'secondary' : 'success' }}">
                                                {{ $review->status == 'confirmed' ? 'Set Pending' : 'Confirm' }}
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center">No recent reviews.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Orders</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($recent_orders as $order)
                                <li class="list-group-item">
                                    <a href="{{ route('order.show', $order->id) }}">
                                        Order #{{ $order->id }} - Rs{{ number_format($order->grand_total, 2) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Recently Added Products -->
            <div class="col-lg-6 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">Recently Added Products</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($recent_products as $product)
                                <li class="list-group-item">
                                    <strong>
                                        <a href="{{ route('product.show', $product->id) }}">
                                            {{ $product->name }} - Rs{{ number_format($product->price, 2) }}
                                        </a>
                                    </strong>
                                    @if ($product->variants->count() > 0)
                                        <ul class="mt-2">
                                            @foreach ($product->variants as $variant)
                                                <li>
                                                    Variant: {{ $variant->sku }} -
                                                    Rs{{ number_format($variant->price, 2) }} (Stock:
                                                    {{ $variant->stock_quantity }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Recent Users -->
            <div class="col-lg-6 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Users</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach ($recent_users as $user)
                                <li class="list-group-item">
                                    {{ $user->name }} - {{ $user->email }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.dashboardData = {
            topKeywordsLabels: {!! json_encode(collect($top_keywords)->pluck('keyword')) !!},
            topKeywordsData: {!! json_encode(collect($top_keywords)->pluck('count')) !!},
            topProductsLabels: {!! json_encode(collect($top_viewed_products)->pluck('product_name')) !!},
            topProductsData: {!! json_encode(collect($top_viewed_products)->pluck('count')) !!},
            gaViewsLabels: {!! json_encode(collect($ga_views_trend_30)->pluck('date')) !!},
            gaViewsData: {!! json_encode(collect($ga_views_trend_30)->pluck('views')) !!},
            userTrendLabels: {!! json_encode(collect($user_trend)->pluck('date')) !!},
            userTrendData: {!! json_encode(collect($user_trend)->pluck('users')) !!}
        };
    </script>
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>
@endpush

@section('right-sidebar')
    <div class="p-3">
        <h5>User Activities</h5>
        <ul class="list-group">
            @forelse ($activities as $activity)
                <li class="list-group-item">
                    {{ $activity->causer ? $activity->causer->name : 'Someone' }}
                    {{ $activity->description }}
                    {{ $activity->subject && $activity->subject->name && $activity->subject->name != '' ? $activity->subject->name : 'a ' . last(explode('\\', $activity->subject_type)) }}
                    <small class="text-muted">({{ $activity->created_at->diffForHumans() }})</small>
                </li>
            @empty
                <li class="list-group-item text-center">No recent activities.</li>
            @endforelse
        </ul>
    </div>
@endsection

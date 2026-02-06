@extends('adminlte::page')

@section('title', 'All Reviews')

@section('content_header')
    <h1>All Reviews</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td>{{ $review->user ? $review->user->name : 'Anonymous' }}</td>
                            <td>
                                <a href="{{ route('product.show', $review->product_id) }}">
                                    {{ $review->product ? $review->product->name : 'Product' }}
                                </a>
                            </td>
                            <td>{{ $review->rating }}</td>
                            <td>{{ Str::limit($review->review, 80) }}</td>
                            <td>
                                <span class="badge badge-{{ $review->status == 'confirmed' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </td>
                            <td>{{ $review->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.review.toggle-status', $review->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $review->status == 'confirmed' ? 'secondary' : 'success' }}">
                                        {{ $review->status == 'confirmed' ? 'Set Pending' : 'Confirm' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
@stop

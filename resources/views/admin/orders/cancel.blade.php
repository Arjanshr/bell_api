@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Cancel Order #{{ $order->id }}</h3>
        </div>
        <div class="card-body">
            @if($order->cancellation)
                <div class="alert alert-info">
                    <strong>Already Cancelled</strong>
                    <p>Category: {{ $order->cancellation->category->name ?? '-' }}</p>
                    <p>Reason: {{ $order->cancellation->reason ?? '-' }}</p>
                    <p>By: {{ $order->cancellation->admin->name ?? '-' }}</p>
                </div>
                <a href="{{ route('orders') }}" class="btn btn-secondary">Back to Orders</a>
            @else
                <form action="{{ route('order.cancel.store', $order) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="order_cancellation_category_id">Cancellation Category</label>
                        <select name="order_cancellation_category_id" id="order_cancellation_category_id" class="form-control" required>
                            <option value="">Select reason</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('order_cancellation_category_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="reason">Reason (optional)</label>
                        <textarea name="reason" id="reason" rows="4" class="form-control">{{ old('reason') }}</textarea>
                        @error('reason')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-danger">Cancel Order</button>
                        <a href="{{ route('orders') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection

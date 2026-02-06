@extends('adminlte::page')

@section('title', 'Product View Log Report')

@section('content_header')
    <h1>Product View Log Report</h1>
@stop

@section('content')
<div class="container-fluid">
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-2">
                <select name="time_frame" class="form-control">
                    <option value="daily" {{ request('time_frame') == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('time_frame') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('time_frame', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-control">
                    <option value="all" {{ request('type', 'all') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="guest" {{ request('type') == 'guest' ? 'selected' : '' }}>Guest</option>
                    <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="group_by" class="form-control">
                    <option value="user" {{ request('group_by', 'user') == 'user' ? 'selected' : '' }}>Group by User</option>
                    <option value="product" {{ request('group_by') == 'product' ? 'selected' : '' }}>Group by Product</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <h4>Guest Views</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guestReport as $row)
                <tr>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->count }}</td>
                </tr>
            @empty
                <tr><td colspan="2">No data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>User Views</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                @if($groupBy === 'product')
                    <th>Product</th>
                    <th>Count</th>
                @else
                    <th>User</th>
                    <th>Product</th>
                    <th>Count</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($userReport as $row)
                <tr>
                    @if($groupBy === 'product')
                        <td>{{ $row->product_name }}</td>
                        <td>{{ $row->count }}</td>
                    @else
                        <td>{{ $row->user_name }}</td>
                        <td>{{ $row->product_name }}</td>
                        <td>{{ $row->count }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $groupBy === 'product' ? 2 : 3 }}">No data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@stop
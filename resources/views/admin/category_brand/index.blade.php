{{-- filepath: d:\xampp\htdocs\mobile-mandu\resources\views\admin\category_brand\index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Category-Brand Relations')

@section('content_header')
    <h1>Category-Brand Relations</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('category-brand.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Create New Relation
        </a>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Summary</th>
                        <th>Description</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($relations as $relation)
                        <tr>
                            <td>{{ $relation->category_name }}</td>
                            <td>{{ $relation->brand_name }}</td>
                            <td>{{ Str::limit($relation->summary, 40) }}</td>
                            <td>{!! Str::limit($relation->description, 40) !!}</td>
                            <td>
                                <a href="{{ route('category-brand.edit', [$relation->category_id, $relation->brand_id]) }}" class="btn btn-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('category-brand.delete', [$relation->category_id, $relation->brand_id]) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this relation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No category-brand relations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
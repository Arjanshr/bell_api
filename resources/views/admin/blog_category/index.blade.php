@extends('adminlte::page')

@section('title', 'Blog Categories')

@section('content_header')
    <h1>Blog Categories</h1>
@stop

@section('content')
    <a href="{{ route('blog-categories.create') }}" class="btn btn-primary mb-3">Add Category</a>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td>{{ $cat->name }}</td>
                        <td>{{ $cat->slug }}</td>
                        <td>
                            <a href="{{ route('blog-categories.edit', $cat->id) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('blog-categories.destroy', $cat->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $categories->links() }}
        </div>
    </div>
@stop

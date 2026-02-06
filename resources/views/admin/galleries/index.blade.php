@extends('adminlte::page')

@section('title', 'Galleries')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <h3>Galleries</h3>
        <a href="{{ route('gallery.create') }}" class="btn btn-primary">Create Gallery</a>
    </div>

    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Search">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary">Search</button>
            </div>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Status</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($galleries as $gallery)
            <tr>
                <td>{{ $gallery->title }}</td>
                <td>{{ $gallery->type }}</td>
                <td>{{ $gallery->status ? 'Active' : 'Inactive' }}</td>
                <td>{{ $gallery->images()->count() }}</td>
                <td>
                    <a href="{{ route('gallery.edit', $gallery->id) }}" class="btn btn-sm btn-info">Edit</a>
                    <a href="{{ route('gallery.images', $gallery->id) }}" class="btn btn-sm btn-secondary">Images</a>
                    <form action="{{ route('gallery.delete', $gallery->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this gallery?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $galleries->links() }}
</div>
@endsection

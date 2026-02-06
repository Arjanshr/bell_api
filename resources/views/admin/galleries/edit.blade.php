@extends('adminlte::page')

@section('title', 'Edit Gallery')

@section('content')
<div class="container-fluid">
    <h3>Edit Gallery</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gallery.update', $gallery->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $gallery->title) }}">
        </div>
        <div class="form-group">
            <label>Type</label>
            <input type="text" name="type" class="form-control" value="{{ old('type', $gallery->type) }}">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description', $gallery->description) }}</textarea>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1" {{ $gallery->status ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$gallery->status ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('galleries') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

@extends('adminlte::page')

@section('title', 'Create Gallery')

@section('content')
<div class="container-fluid">
    <h3>Create Gallery</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}">
        </div>
        <div class="form-group">
            <label>Type</label>
            <input type="text" name="type" class="form-control" value="{{ old('type') }}">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label>Images</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <button class="btn btn-primary">Create</button>
        <a href="{{ route('galleries') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

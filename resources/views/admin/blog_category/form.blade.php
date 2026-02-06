@extends('adminlte::page')

@section('title', isset($blogCategory) ? 'Edit Blog Category' : 'Add Blog Category')

@section('content_header')
    <h1>{{ isset($blogCategory) ? 'Edit' : 'Add' }} Blog Category</h1>
@stop

@section('content')
    <form method="POST"
          action="{{ isset($blogCategory) ? route('blog-categories.update', $blogCategory->id) : route('blog-categories.store') }}">
        @csrf
        @if(isset($blogCategory))
            @method('PATCH')
        @endif
        <div class="form-group">
            <label for="name">Category Name*</label>
            <input type="text" name="name" class="form-control" value="{{ isset($blogCategory) ? $blogCategory->name : old('name') }}" required>
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button class="btn btn-primary" type="submit">{{ isset($blogCategory) ? 'Update' : 'Create' }}</button>
        <a href="{{ route('blog-categories.index') }}" class="btn btn-secondary">Back</a>
    </form>
@stop

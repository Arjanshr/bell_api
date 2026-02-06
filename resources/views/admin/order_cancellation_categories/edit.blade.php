@extends('adminlte::page')

@section('title', 'Edit Cancellation Category')

@section('content_header')
    <h1>Edit Cancellation Category</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Cancellation Category</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('order-cancellation-categories.update', $category->id) }}" method="post">
            @csrf
            @method('patch')
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $category->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$category->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Save</button>
            <a href="{{ route('order-cancellation-categories') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@stop

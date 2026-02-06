@extends('adminlte::page')

@section('title', 'Create Cancellation Category')

@section('content_header')
    <h1>Create Cancellation Category</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create Cancellation Category</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('order-cancellation-categories.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Save</button>
            <a href="{{ route('order-cancellation-categories') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
</div>
@stop

@extends('adminlte::page')

@section('title', 'Add Setting')

@section('content_header')
    <h1>Add New Setting</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('setting.insert') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.settings._form')
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('setting.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop

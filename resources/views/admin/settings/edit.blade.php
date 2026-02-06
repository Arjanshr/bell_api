@extends('adminlte::page')

@section('title', 'Edit Setting')

@section('content_header')
    <h1>Edit Setting: {{ $setting->key }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('setting.update', $setting) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('admin.settings._form')
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('setting.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@stop

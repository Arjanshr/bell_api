@extends('adminlte::page')

@section('title', 'Edit Province')

@section('content_header')
	<h1>Edit Province</h1>
@endsection

@section('content')
	<form action="{{ route('locations.provinces.update', $province->id) }}" method="POST">
		@csrf
		@method('PUT')
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="form-control" value="{{ old('name', $province->name) }}" required>
		</div>
		<button type="submit" class="btn btn-success">Update</button>
	<a href="{{ route('locations.provinces.index') }}" class="btn btn-secondary">Cancel</a>
	</form>
@endsection

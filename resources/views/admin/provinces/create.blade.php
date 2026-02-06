@extends('adminlte::page')

@section('title', 'Add Province')

@section('content_header')
	<h1>Add Province</h1>
@endsection

@section('content')
	<form action="{{ route('locations.provinces.store') }}" method="POST">
		@csrf
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
		</div>
		<button type="submit" class="btn btn-primary">Add</button>
	<a href="{{ route('locations.provinces.index') }}" class="btn btn-secondary">Cancel</a>
	</form>
@endsection

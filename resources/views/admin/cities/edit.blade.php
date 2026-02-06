@extends('adminlte::page')

@section('title', 'Edit City')

@section('content_header')
	<h1>Edit City</h1>
@endsection

@section('content')
	<form action="{{ route('locations.cities.update', $city->id) }}" method="POST">
		@csrf
		@method('PUT')
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="form-control" value="{{ old('name', $city->name) }}" required>
		</div>
		<div class="form-group">
			<label for="province_id">Province</label>
			<select name="province_id" class="form-control" required>
				@foreach(App\Models\Province::all() as $province)
					<option value="{{ $province->id }}" {{ $city->province_id == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
				@endforeach
			</select>
		</div>
		<button type="submit" class="btn btn-success">Update</button>
	<a href="{{ route('locations.cities.index') }}" class="btn btn-secondary">Cancel</a>
	</form>
@endsection

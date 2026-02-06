@extends('adminlte::page')

@section('title', 'Edit Area')

@section('content_header')
	<h1>Edit Area</h1>
@endsection

@section('content')
	<form action="{{ route('locations.areas.update', $area->id) }}" method="POST">
		@csrf
		@method('PUT')
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="form-control" value="{{ old('name', $area->name) }}" required>
		</div>
		<div class="form-group">
			<label for="city_id">City</label>
			<select name="city_id" class="form-control" required>
				@foreach(App\Models\City::all() as $city)
					<option value="{{ $city->id }}" {{ $area->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<label for="shipping_price">Shipping Price</label>
			<input type="number" step="0.01" name="shipping_price" class="form-control" value="{{ old('shipping_price', $area->shipping_price) }}" required>
		</div>
		<button type="submit" class="btn btn-success">Update</button>
	<a href="{{ route('locations.areas.index') }}" class="btn btn-secondary">Cancel</a>
	</form>
@endsection

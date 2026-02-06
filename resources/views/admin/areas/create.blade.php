@extends('adminlte::page')

@section('title', 'Add Area')

@section('content_header')
	<h1>Add Area</h1>
@endsection

@section('content')
	<form action="{{ route('locations.areas.store') }}" method="POST">
		@csrf
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
		</div>
		<div class="form-group">
			<label for="city_id">City</label>
			<select name="city_id" class="form-control" required>
				@foreach(App\Models\City::all() as $city)
					<option value="{{ $city->id }}">{{ $city->name }}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group">
			<label for="shipping_price">Shipping Price</label>
			<input type="number" step="0.01" name="shipping_price" class="form-control" value="{{ old('shipping_price') }}" required>
		</div>
		<button type="submit" class="btn btn-primary">Add</button>
	<a href="{{ route('locations.areas.index') }}" class="btn btn-secondary">Cancel</a>
	</form>
@endsection

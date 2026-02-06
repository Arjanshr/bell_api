@extends('adminlte::page')

@section('title', 'Add City')

@section('content_header')
	<h1>Add City</h1>
@endsection

@section('content')
	<form action="{{ route('locations.cities.store') }}" method="POST">
		@csrf
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
		</div>
		<div class="form-group">
			<label for="province_id">Province</label>
			<select name="province_id" class="form-control" required>
				@foreach(App\Models\Province::all() as $province)
					<option value="{{ $province->id }}">{{ $province->name }}</option>
				@endforeach
			</select>
		</div>
		<button type="submit" class="btn btn-primary">Add</button>
	<a href="{{ route('locations.cities.index') }}" class="btn btn-secondary">Cancel</a>
	</form>
@endsection

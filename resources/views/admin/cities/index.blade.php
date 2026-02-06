@extends('adminlte::page')

@section('title', 'Cities')

@section('content_header')
	<h1>Cities</h1>
@endsection

@section('content')
	@if(session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif
	<div class="card">
		<div class="card-body">
			<div class="mb-3 d-flex align-items-center">
				<a href="{{ route('locations.cities.create') }}" class="btn btn-primary mr-3">Add City</a>
				<form method="GET" action="" class="form-inline d-flex flex-wrap align-items-center">
					<label for="province_id" class="mr-2">Filter by Province:</label>
					<select name="province_id" id="province_id" class="form-control mr-2" onchange="this.form.submit()">
						<option value="">All Provinces</option>
						@foreach($provinces as $province)
							<option value="{{ $province->id }}" @if(isset($provinceId) && $provinceId == $province->id) selected @endif>{{ $province->name }}</option>
						@endforeach
					</select>
					<label for="search" class="mr-2 mb-0">Search:</label>
					<input type="text" name="search" id="search" class="form-control mr-2" value="{{ isset($search) ? $search : '' }}" placeholder="City name...">
					<button type="submit" class="btn btn-secondary btn-sm">Go</button>
				</form>
			</div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Province</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					@foreach($cities as $city)
						<tr>
							<td>{{ $city->id }}</td>
							<td>{{ $city->name }}</td>
							<td>{{ $city->province ? $city->province->name : '-' }}</td>
							<td>
								<a href="{{ route('locations.cities.edit', $city->id) }}" class="btn btn-sm btn-warning">Edit</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
@endsection

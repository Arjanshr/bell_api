@extends('adminlte::page')

@section('title', 'Provinces')

@section('content_header')
	<h1>Provinces</h1>
@endsection

@section('content')
	@if(session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif
	<div class="card">
		<div class="card-body">
			<div class="mb-3">
				<a href="{{ route('locations.provinces.create') }}" class="btn btn-primary">Add Province</a>
			</div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					@foreach($provinces as $province)
						<tr>
							<td>{{ $province->id }}</td>
							<td>{{ $province->name }}</td>
							<td>
								<a href="{{ route('locations.provinces.edit', $province->id) }}" class="btn btn-sm btn-warning">Edit</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
@endsection

@extends('adminlte::page')

@section('title', 'Areas')


@section('content_header')
    <h1>Areas</h1>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="mb-3 d-flex align-items-center flex-wrap">
                <a href="{{ route('locations.areas.create') }}" class="btn btn-primary mr-3">Add Area</a>
                <form method="GET" action="" class="form-inline d-flex flex-wrap align-items-center"
                    id="areas-filter-form">
                    <label for="province_id" class="mr-2">Province:</label>
                    <select name="province_id[]" id="province_id" class="form-control mr-2" multiple>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->id }}" @if (isset($provinceId) && (is_array($provinceId) ? in_array($province->id, $provinceId) : $provinceId == $province->id)) selected @endif>
                                {{ $province->name }}</option>
                        @endforeach
                    </select>
                    <label for="city_id" class="mr-2 mb-0">City:</label>
                    <select name="city_id[]" id="city_id" class="form-control mr-2" multiple
                        data-cities='@json($cities)'>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" @if (isset($cityId) && (is_array($cityId) ? in_array($city->id, $cityId) : $cityId == $city->id)) selected @endif>
                                {{ $city->name }}</option>
                        @endforeach
                    </select>
                    <label for="search" class="mr-2 mb-0">Search:</label>
                    <input type="text" name="search" id="search" class="form-control mr-2"
                        value="{{ isset($search) ? $search : '' }}" placeholder="Area name...">
                    <button type="submit" class="btn btn-secondary btn-sm">Go</button>
                </form>
            </div>
            @can('edit-areas')
                <form id="mass-update-form" method="POST" action="{{ route('locations.areas.massUpdatePrice') }}">
                    @csrf
                    <div class="mb-3 d-flex align-items-center flex-wrap">
                        <select name="operation" id="operation" class="form-control mr-2" style="width:120px;">
                            <option value="set">Set</option>
                            <option value="add">Add</option>
                            <option value="subtract">Subtract</option>
                        </select>
                        <input type="number" step="0.01" min="0" name="new_price" id="new_price"
                            class="form-control mr-2" placeholder="Amount" style="width:180px;">
                        <button type="submit" class="btn btn-success btn-sm mr-2" id="mass-update-btn">Mass Update
                            Price</button>
                        @can('delete-areas')
                            <button type="button" class="btn btn-danger btn-sm mr-2" id="mass-delete-btn">Delete Selected</button>
                        @endcan
                        <span id="mass-update-message" class="ml-2"></span>
                    </div>
                </form>
            @endcan
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Shipping Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($areas as $area)
                        <tr>
                            <td><input type="checkbox" class="area-checkbox" name="area_ids[]" value="{{ $area->id }}">
                            </td>
                            <td>{{ $area->id }}</td>
                            <td>{{ $area->name }}</td>
                            <td>{{ $area->city ? $area->city->name : '-' }}</td>
                            <td>{{ $area->shipping_price }}</td>
                            <td>
                                <a href="{{ route('locations.areas.edit', $area->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </form>
        </div>
    </div>
@stop
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single,
        .select2-container .select2-selection--multiple {
            font-size: 1.15rem !important;
            /* padding: 2px !important;Ok */
            line-height: 2.2 !important;
            display: flex;
            align-items: center;
        }

        .select2-container .select2-search--inline .select2-search__field {
            min-height: 36px !important;
            font-size: 1.15rem !important;
            padding: 6px 8px !important;
            margin: 0 !important;
            box-sizing: border-box;
        }

        .select2-container .select2-selection--multiple .select2-search__field {
            height: 36px !important;
        }

        .select2-container {
            width: 300px !important;
            max-width: 100%;
        }

        .select2-selection__choice__display {
            padding-left: 15px !important;
        }

        .select2-selection__choice {
            color: #000 !important;
        }

        .select2-selection__choice__remove {
            color: rgb(201, 14, 14) !important;
            font-size: larger !important;
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#province_id').select2({
                placeholder: 'Select Province(s)'
            });
            $('#city_id').select2({
                placeholder: 'Select City/Cities'
            });

            function filterCities() {
                var selectedProvinces = $('#province_id').val();
                var allCities = JSON.parse($('#city_id').attr('data-cities'));
                var filtered = [];
                if (selectedProvinces && selectedProvinces.length > 0) {
                    filtered = allCities.filter(function(city) {
                        return selectedProvinces.includes(city.province_id.toString());
                    });
                } else {
                    filtered = allCities;
                }
                var citySelect = $('#city_id');
                var selectedCities = citySelect.val() || [];
                citySelect.empty();
                filtered.forEach(function(city) {
                    var selected = selectedCities.includes(city.id.toString()) ? 'selected' : '';
                    citySelect.append('<option value="' + city.id + '" ' + selected + '>' + city.name +
                        '</option>');
                });
                citySelect.trigger('change.select2');
            }

            $('#province_id').on('change', filterCities);
            filterCities();
        });
        // Select all functionality
        $('#select-all').on('change', function() {
            $('.area-checkbox').prop('checked', $(this).prop('checked'));
        });
        $('.area-checkbox').on('change', function() {
            if (!$(this).prop('checked')) {
                $('#select-all').prop('checked', false);
            } else if ($('.area-checkbox:checked').length === $('.area-checkbox').length) {
                $('#select-all').prop('checked', true);
            }
        });

        // Mass update price AJAX
        $('#mass-update-form').on('submit', function(e) {
            e.preventDefault();
            var areaIds = $('.area-checkbox:checked').map(function() {
                return this.value;
            }).get();
            var newPrice = $('#new_price').val();
            var token = $('input[name="_token"]').val();
            var operation = $('#operation').val();
            if (areaIds.length === 0 || !newPrice) {
                $('#mass-update-message').text('Select at least one area and enter a price.').css('color', 'red');
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will update the shipping price for the selected areas.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: $('#mass-update-form').attr('action'),
                        method: 'POST',
                        data: {
                            _token: token,
                            area_ids: areaIds,
                            new_price: newPrice,
                            operation: operation
                        },
                        success: function(resp) {
                            Swal.fire('Updated!', resp.message || 'Prices updated!', 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1200);
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Error updating prices.', 'error');
                        }
                    });
                }
            });
        });
        // Mass delete logic
        $('#mass-delete-btn').on('click', function() {
            var areaIds = $('.area-checkbox:checked').map(function() {
                return this.value;
            }).get();
            var token = $('input[name="_token"]').val();
            if (areaIds.length === 0) {
                Swal.fire('No selection', 'Select at least one area to delete.', 'warning');
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete the selected areas.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('locations.areas.massDelete') }}",
                        method: 'POST',
                        data: {
                            _token: token,
                            area_ids: areaIds
                        },
                        success: function(resp) {
                            Swal.fire('Deleted!', resp.message || 'Areas deleted!', 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1200);
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Error deleting areas.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@stop

@extends('adminlte::page')

@section('title', 'Products')

@section('content_header')
    <h1>Products</h1>
@stop
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @can('add-products')
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-md-4 mb-2">
                                        <a href="{{ route('product.create') }}" class="btn btn-success btn-sm" title="Create Product">
                                            <i class="fas fa-plus-circle"></i> Create Product
                                        </a>
                                        @can('export-products')
                                        <a href="{{ route('product.export', request()->query()) }}" class="btn btn-info btn-sm ml-2" title="Export to CSV">
                                            <i class="fas fa-file-csv"></i>
                                        </a>
                                        @endcan
                                    </div>
                                    @can('import-products')
                                    <div class="col-md-4 mb-2 ml-auto">
                                        <form action="{{ route('product.import') }}" method="post" enctype="multipart/form-data" class="d-flex justify-content-end">
                                            @csrf
                                            <input type="file" name="import_file" class="form-control-file mr-2">
                                            <button type="submit" class="btn btn-primary btn-sm" title="Import">
                                                <i class="fas fa-upload"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @endcan
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <form>
                                            <div class="row align-items-end">
                                                <!-- Brand -->
                                                <div class="form-group col-sm-3 mb-0">
                                                    <label for="brand_id" class="font-weight-bold">Brand</label>
                                                    <select id="brand_id" name="brand_id" class="form-control select2">
                                                        <option value="">Select a Brand</option>
                                                        @foreach ($brands as $brand)
                                                            <option value="{{ $brand->id }}"
                                                                {{ (isset($selected_brand) && $selected_brand == $brand->id) || old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                                {{ $brand->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- Categories -->
                                                <div class="form-group col-sm-4 mb-0">
                                                    <label for="categories" class="font-weight-bold">Categories</label>
                                                    <select name="category_id[]" id="categories" class="form-control select2" multiple>
                                                        <option value="">--select--</option>
                                                        @if (isset($categories))
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}"
                                                                    @if (isset($selected_categories) && in_array($category->id, $selected_categories)) selected @endif>
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <!-- Keyword -->
                                                <div class="form-group col-sm-4 mb-0 d-flex align-items-end">
                                                    <div style="width:100%;">
                                                        <div class="d-flex align-items-center">
                                                            <label for="query" class="font-weight-bold mb-0">Keyword</label>
                                                            <small class="form-text text-muted ml-2 mb-0" style="white-space:nowrap; color: #b22222;">
                                                                (Separate multiple names with <b>|||</b>)
                                                            </small>
                                                        </div>
                                                        <input type="text" name="query" value="{{ $query ?? '' }}" class="form-control"
                                                            placeholder="Search by keyword">
                                                    </div>
                                                </div>
                                                <!-- Filter Button -->
                                                <div class="form-group col-sm-1 d-flex mb-0" style="align-items: flex-end;">
                                                    <button id="submit" type="submit" class="btn btn-warning btn-sm btn-block" title="Apply Filters" style="height: 40px;">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                                <!-- Only Unpublished Checkbox -->
                                                <div class="form-group col-sm-2 d-flex align-items-end mb-0">
                                                    <div class="custom-control custom-switch" style="margin-left: 10px;">
                                                        <input class="custom-control-input" type="checkbox" name="only_unpublished" id="only_unpublished"
                                                            value="1" {{ request('only_unpublished') ? 'checked' : '' }}>
                                                        <label class="custom-control-label font-weight-bold" for="only_unpublished" style="cursor:pointer; color: #b22222;">
                                                            Only Unpublished
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endcan
                        <div class="card-body">
                            <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="items_per_page">Show</label>
                                            <select id="items_per_page" name="items_per_page" class="form-control form-control-sm d-inline-block" style="width: auto;" onchange="location = this.value;">
                                                <option value="{{ request()->fullUrlWithQuery(['items_per_page' => 10]) }}" {{ request('items_per_page') == 10 ? 'selected' : '' }}>10</option>
                                                <option value="{{ request()->fullUrlWithQuery(['items_per_page' => 25]) }}" {{ request('items_per_page') == 25 ? 'selected' : '' }}>25</option>
                                                <option value="{{ request()->fullUrlWithQuery(['items_per_page' => 50]) }}" {{ request('items_per_page') == 50 ? 'selected' : '' }}>50</option>
                                                <option value="{{ request()->fullUrlWithQuery(['items_per_page' => 100]) }}" {{ request('items_per_page') == 100 ? 'selected' : '' }}>100</option>
                                            </select>
                                            <span>entries</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <!-- Existing search or other controls can go here -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table id="example2" class="table table-bordered table-hover table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Actions</th>
                                                    <th>Name</th>
                                                    <th>SKU</th>
                                                    <th>Price</th>
                                                    <th>Availability</th>
                                                    <th>Category</th>
                                                    <th>Brand</th>
                                                    <th>Image</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($products as $product)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <div class="d-flex flex-wrap align-items-center" style="gap: 0.5rem;">
                                                                @can('read-products')
                                                                    <a href="{{ route('product.show', $product->id) }}" class="btn btn-sm btn-primary" title="View Details">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('edit-products')
                                                                    <a href="{{ route('product.edit', $product->id) }}" class="btn btn-sm btn-success" title="Edit Product">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('product.specifications', $product->id) }}" class="btn btn-sm btn-warning" title="Manage Specifications">
                                                                        <i class="fas fa-cogs"></i>
                                                                    </a>
                                                                    <a href="{{ route('product.features', $product->id) }}" class="btn btn-sm btn-primary" title="Manage Features">
                                                                        <i class="fas fa-list-alt"></i>
                                                                    </a>
                                                                    <a href="{{ route('product.variants', $product->id) }}" class="btn btn-sm btn-success" title="Manage Variants">
                                                                        <i class="fas fa-clone"></i>
                                                                    </a>
                                                                    <a href="{{ route('product.images', $product->id) }}" class="btn btn-sm btn-secondary" title="Manage Images">
                                                                        <i class="fas fa-images"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('delete-products')
                                                                    <form method="post" action="{{ route('product.delete', $product->id) }}" style="display: inline;">
                                                                        @csrf
                                                                        @method('delete')
                                                                        <button class="delete btn btn-danger btn-sm" type="submit" title="Delete Product">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                        <td>{{ $product->name }}</td>
                                                        <td>{{ $product->sku ?? '-' }}</td>
                                                        <td>Rs {{ number_format($product->price, 2) }}</td>
                                                        <td>
                                                            @if($product->in_stock)
                                                                <span class="badge badge-success">In Stock</span>
                                                            @else
                                                                <span class="badge badge-danger">Out of Stock</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @foreach ($product->categories as $category)
                                                                <span class="badge badge-info">{{ $category->name }}</span>
                                                            @endforeach
                                                        </td>
                                                        <td>{{ $product->brand ? $product->brand->name : 'N/A' }}</td>
                                                        <td>
                                                            @if ($product->getFirstMedia())
                                                                <img src="{{ $product->getFirstMedia()->getUrl() }}" class="img-thumbnail" style="height: 100px; object-fit: cover;" />
                                                            @else
                                                                <span class="text-muted">No Image</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-{{ $product->status == 'publish' ? 'success' : ($product->status == 'active' ? 'success' : 'danger') }}">
                                                                {{ ucfirst($product->status) }}ed
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{ $products->appends($_GET)->links() }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
@stop

@section('css')
    <style>
        .select2-container .select2-selection--single {
            height: 35px !important;
        }

        .select2-selection__arrow {
            height: 34px !important;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#categories').select2();
        });
    </script>
    <script>
        $(document.body).on('click', '.delete', function(event) {
            event.preventDefault();
            var form = $(this).closest("form");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX delete
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            // Remove the row from the table
                            form.closest('tr').remove();
                            Swal.fire('Deleted!', 'Product has been deleted.', 'success');
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            })
        });
    </script>
@stop

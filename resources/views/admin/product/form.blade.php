@extends('adminlte::page')

@section('title', 'Products')

@section('content_header')
    <h1>Products</h1>
@stop

@section('content')


    <div class="card-body">
        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-12">
                        @if (isset($product))
                            @livewire('product-form', ['product' => $product])
                        @else
                            @livewire('product-form');
                        @endif
                    </div>

                </div>

            </div>
        </section>
    </div>


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
    <!-- Removed Tagify CSS -->
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#category').select2();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#categories').select2();
        });
    </script>
    <!-- Removed Tagify JS -->
@stop

@extends('adminlte::page')

@section('title', 'Categories')

@section('content_header')
    <h1>Categories</h1>
@stop

@section('content')


    <div class="card-body">
        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-12">
                        @if (isset($category))
                            @livewire('category-form', ['category' => $category, 'parent_categories' => $parent_categories])
                        @else
                            @livewire('category-form', ['parent_categories' => $parent_categories])
                        @endif
                    </div>

                </div>

            </div>
        </section>
    </div>


@stop

@section('css')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#role').select2();
        });
    </script>
@stop

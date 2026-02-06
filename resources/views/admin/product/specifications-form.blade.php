@extends('adminlte::page')

@section('title', 'Specifications')

@section('content_header')
    <h1>Specifications ({{ $product->name }})</h1>
@stop

@section('content')
    <div class="card-body">
        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header" style="display: flex; align-items: center;">
                                <h3 class="card-title mb-0">
                                    {{ isset($product_specification) ? 'Edit Specification' : 'Create New Specification' }}
                                </h3>
                                <div style="margin-left: auto; display: flex; align-items: center; gap: 0.5rem;">
                                    <button type="button" id="go-back-btn" class="btn btn-light p-0 d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;border-radius:50%;" title="Back">
                                        <span class="fas fa-arrow-left"></span>
                                    </button>
                                    @if (!isset($product_specification))
                                        <button type="button" id="go-next-btn" class="btn btn-light p-0 d-flex align-items-center justify-content-center"
                                            style="width:32px;height:32px;border-radius:50%;" title="Next">
                                            <span class="fas fa-arrow-right"></span>
                                        </button>
                                    @endif
                                    <a href="{{ route('product.specifications', $product->id) }}"
                                       class="btn btn-danger p-0 d-flex align-items-center justify-content-center"
                                       style="width:32px;height:32px;border-radius:50%;font-weight:bold;border:2px solid #dc3545;"
                                       title="Exit">
                                        <span class="fas fa-times" style="font-size:1.2em;"></span>
                                    </a>
                                </div>
                            </div>

                            <form method="POST"
                                  action="{{ isset($product_specification) ? route('product.specification.update', $product_specification->id) : route('product.specification.insert', $product->id) }}">
                                @csrf
                                @if (isset($product_specification))
                                    @method('patch')
                                @endif

                                <div class="card-body row">
                                    @foreach ($specifications as $specification)
                                        @php
                                            $is_required = $specification->pivot->is_required ?? false;
                                            $field_name = "value[{$specification->id}]";
                                            $field_value = old("value.{$specification->id}", $product_specifications[$specification->id] ?? '');
                                        @endphp

                                        <div class="form-group col-sm-6">
                                            @if ($loop->first)
                                                <label>Name</label>
                                            @endif
                                            <input type="text" class="form-control" placeholder="Name"
                                                   value="{{ $specification->name }}" readonly>
                                        </div>

                                        <div class="form-group col-sm-6">
                                            @if ($loop->first)
                                                <label>Value {!! $is_required ? '<span class="text-danger">*</span>' : '' !!}</label>
                                            @endif
                                            <input type="text"
                                                   class="form-control {{ $is_required ? 'is-required' : '' }}"
                                                   name="{{ $field_name }}"
                                                   placeholder="Value{{ $is_required ? ' *' : '' }}"
                                                   value="{{ $field_value }}"
                                                   {{ $is_required ? 'required' : '' }}>
                                            @error("value.{$specification->id}")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach

                                    <div class="form-group col-sm-12">
                                        @if (!isset($product_specification))
                                            <button type="submit" name="action" value="next" class="btn btn-primary">Create and Next</button>
                                            <button type="submit" name="action" value="exit" class="btn btn-secondary">Create and Exit</button>
                                            <a href="{{ route('product.specifications', $product->id) }}" class="btn btn-danger">Exit</a>
                                        @else
                                            <input id="submit" type="submit" value="Edit" class="btn btn-primary" />
                                            <a href="{{ route('product.specifications', $product->id) }}" class="btn btn-danger">Exit</a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        .is-required {
            border-left: 3px solid red;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#role').select2();

            $('#go-next-btn').on('click', function () {
                window.location.href = "{{ route('product.feature.create', $product->id) }}";
            });

            $('#go-back-btn').on('click', function () {
                window.history.back();
            });
        });
    </script>
@stop
pCategory
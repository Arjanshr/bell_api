@extends('adminlte::page')

@section('title', 'Features')

@section('content_header')
    <h1>Features({{ $product->name }})</h1>
@stop

@section('content')


    <div class="card-body">
        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header" style="display: flex; align-items: center;">
                                <h3 class="card-title mb-0">{{ isset($feature) ? 'Edit Feature' : 'Create New Feature' }}</h3>
                                <div style="margin-left: auto; display: flex; align-items: center; gap: 0.5rem;">
                                    <button type="button" id="go-back-btn" class="btn btn-light p-0 d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;border-radius:50%;" title="Back">
                                        <span class="fas fa-arrow-left"></span>
                                    </button>
                                    @if (!isset($feature))
                                        <button type="button" id="go-next-btn" class="btn btn-light p-0 d-flex align-items-center justify-content-center"
                                            style="width:32px;height:32px;border-radius:50%;" title="Next">
                                            <span class="fas fa-arrow-right"></span>
                                        </button>
                                    @endif
                                    <a href="{{ route('product.features', $product->id) }}"
                                       class="btn btn-danger p-0 d-flex align-items-center justify-content-center"
                                       style="width:32px;height:32px;border-radius:50%;font-weight:bold;border:2px solid #dc3545;"
                                       title="Exit">
                                        <span class="fas fa-times" style="font-size:1.2em;"></span>
                                    </a>
                                </div>
                            </div>
                            <form method="POST"
                                action="{{ isset($feature) ? route('product.feature.update', [$feature->id]) : route('product.feature.insert', $product->id) }}">
                                @csrf
                                @if (isset($feature))
                                    @method('patch')
                                @endif
                                <div class="card-body row">
                                    <!-- Feature -->
                                    <div class="form-group col-sm-12">
                                        <label for="feature">Feature*</label>
                                        <textarea class="form-control" id="feature" name="feature" placeholder="Feature" rows="5">{{ isset($feature) ? $feature->feature : old('feature') }}</textarea>
                                        @error('feature')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-12">
                                        @if (!isset($feature))
                                            <button type="submit" name="action" value="add_another" class="btn btn-success">Create and Add Another</button>
                                            <button type="submit" name="action" value="next" class="btn btn-primary">Create and Next</button>
                                            <button type="submit" name="action" value="exit" class="btn btn-secondary">Create and Exit</button>
                                            <a href="{{ route('product.features', $product->id) }}" class="btn btn-danger">Exit</a>
                                        @else
                                            <input id="submit" type="submit"
                                                value="Edit" class="btn btn-primary" />
                                            <a href="{{ route('product.features', $product->id) }}" class="btn btn-danger">Exit</a>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
@stop

@section('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#feature'), {
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' }
                ]
            }
        })
        .catch(error => {
            console.error(error);
        });
        $(document).ready(function() {
            $('#role').select2();
            $('#go-next-btn').on('click', function() {
                window.location.href = "{{ route('product.images', $product->id) }}";
            });
            $('#go-back-btn').on('click', function() {
                window.history.back();
            });
        });
    </script>
@stop

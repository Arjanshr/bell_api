@extends('adminlte::page')

@section('title', 'Manage Gallery Images')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Manage Images for: {{ $gallery->title }}</h3>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Upload New Images</h5>
                </div>
                <div class="card-body">
                    <div class="text-muted mb-3">
                        <small>
                            <span class="text-warning">Recommended Image Size: </span><span class="text-danger">1200px x 800px</span><br/>
                            <span class="text-warning">Recommended Formats: </span><span class="text-danger">WebP</span><br/>
                            <span class="text-warning">Max File Size: </span><span class="text-danger">< 1MB</span>
                        </small>
                    </div>
                    <div id="dropzone">
                        <form action="{{ route('gallery.image.insert', $gallery->id) }}" method="POST"
                            class="dropzone" id="file-upload" enctype="multipart/form-data">
                            @csrf
                            <div class="fallback">
                                <input name="images[]" type="file" multiple />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Gallery Images</h5>
                </div>
                <div class="card-body">
                    @if($gallery->images->count() > 0)
                        <table class="table table-striped table-hover" id="images-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Preview</th>
                                    <th>Caption</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gallery->images as $image)
                                    <tr data-id="{{ $image->id }}">
                                        <td><img src="{{ asset('storage/' . $image->image_path) }}" style="height:80px;border-radius:4px;"></td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm image-caption" data-id="{{ $image->id }}" value="{{ $image->caption }}" placeholder="Add caption...">
                                        </td>
                                        <td><input type="number" class="form-control form-control-sm image-order" data-id="{{ $image->id }}" value="{{ $image->sort_order }}" min="1"></td>
                                        <td>
                                            <form action="{{ route('gallery.image.delete', $image->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this image?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info">No images uploaded yet. Upload images to get started.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button id="save-order" class="btn btn-success me-2">
            <i class="fas fa-save"></i> Save Order
        </button>
        <a href="{{ route('galleries') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to galleries
        </a>
    </div>
</div>

@endsection

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <style>
        .dropzone {
            background: #e3e6ff;
            border-radius: 13px;
            margin-left: auto;
            margin-right: auto;
            border: 2px dotted #1833FF;
            margin-top: 20px;
            margin-bottom: 20px;
            padding-top: 40px;
            padding-bottom: 40px;
            max-width: 100%;
            min-height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .dropzone .dz-message {
            margin: 0;
            font-size: 1.1em;
            color: #1833FF;
            font-weight: 500;
        }

        .dz-preview {
            display: none;
        }

        .dz-error-message {
            color: #dc3545;
            font-size: 0.9em;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.js"></script>
    <script>
        Dropzone.options.fileUpload = {
            paramName: "images",
            maxFilesize: 5,
            acceptedFiles: "image/*",
            addRemoveLinks: false,
            uploadMultiple: true,
            parallelUploads: 10,
            init: function() {
                this.on("success", function() {
                    location.reload();
                });
                this.on("error", function(file, errorMessage) {
                    console.error("Upload error:", errorMessage);
                });
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Update caption via AJAX
            document.querySelectorAll('.image-caption').forEach(function (el) {
                el.addEventListener('change', function () {
                    var id = this.dataset.id;
                    var caption = this.value;
                    fetch("{{ url('admin/galleries/images') }}/" + id, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ caption: caption })
                    }).then(r => r.json()).then(data => {
                        if(data.success) {
                            console.log('Caption updated');
                        }
                    }).catch(e => console.error('Error:', e));
                });
            });

            // Save order
            document.getElementById('save-order').addEventListener('click', function () {
                var order = {};
                document.querySelectorAll('.image-order').forEach(function (el) {
                    order[el.dataset.id] = el.value;
                });

                fetch("{{ route('gallery.image.reorder', $gallery->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                }).then(r => r.json()).then(data => {
                    if (data.success) { 
                        location.reload(); 
                    }
                }).catch(e => console.error('Error:', e));
            });
        });
    </script>
@endsection

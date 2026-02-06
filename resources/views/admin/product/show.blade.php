@extends('adminlte::page')

@section('title', $product->name ?? 'Products')

@section('content_header')
    <h1>{{ $product->name }}</h1>
@stop
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-product"></i>
                                Name
                            </h3>
                        </div>

                        <div class="card-body">
                            <blockquote>
                                {{ $product->name }}
                            </blockquote>
                        </div>

                    </div>

                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user"></i>
                                Category
                            </h3>
                        </div>

                        <div class="card-body clearfix">
                            <blockquote>
                                @foreach ($product->categories as $category)
                                    {{ $category->name }}{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            </blockquote>
                        </div>

                    </div>

                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user"></i>
                                Brand
                            </h3>
                        </div>

                        <div class="card-body clearfix">
                            <blockquote>
                                {{ $product->brand ? $product->brand->name : 'NA' }}
                            </blockquote>
                        </div>

                    </div>

                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user"></i>
                                Status
                            </h3>
                        </div>

                        <div class="card-body clearfix">
                            <blockquote>
                                {{ ucfirst($product->status) }}
                            </blockquote>
                        </div>

                    </div>

                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-product"></i>
                                Description
                            </h3>
                        </div>

                        <div class="card-body">
                            <blockquote>
                                {!! $product->description !!}
                            </blockquote>
                        </div>

                    </div>

                </div>


            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-product"></i>
                                Specifications
                            </h3>
                        </div>

                        <div class="card-body">
                            <blockquote>
                                @foreach ($product->specifications as $specification)
                                    @if ($specification->specification)
                                        <p>
                                            {!! $specification->specification->name !!}:{!! $specification->value !!}
                                        </p>
                                    @endif
                                @endforeach
                            </blockquote>
                        </div>

                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-product"></i>
                                Features
                            </h3>
                        </div>

                        <div class="card-body">
                            <blockquote>
                                @foreach ($product->features as $feature)
                                    <p>
                                        {!! $feature->feature !!}
                                    </p>
                                @endforeach
                            </blockquote>
                        </div>

                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-product"></i>
                                Images
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                @foreach ($product->getMedia() as $image)
                                    <div class="col-md-3 mb-3" id="media-{{ $image->uuid }}">
                                        <div class="card border">
                                            <img src="{{ $image->getUrl() }}" class="card-img-top" style="height: 150px; object-fit: cover;" />
                                            <div class="card-body p-2">
                                                <button type="button" class="btn btn-sm btn-warning ai-edit-btn" 
                                                    data-product-id="{{ $product->id }}" 
                                                    data-media-uuid="{{ $image->uuid }}"
                                                    data-toggle="modal" 
                                                    data-target="#aiEditModal">
                                                    <i class="fas fa-magic"></i> AI Edit
                                                </button>
                                                <small class="text-muted d-block mt-1">{{ $image->uuid }}</small>
                                                @if ($image->getCustomProperty('ai_mode'))
                                                    <span class="badge badge-info">AI: {{ $image->getCustomProperty('ai_mode') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
@stop

@section('css')
@stop

@section('js')
<!-- AI Edit Modal -->
<div class="modal fade" id="aiEditModal" tabindex="-1" role="dialog" aria-labelledby="aiEditModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aiEditModalLabel">AI Image Editor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="aiEditForm">
                    <div class="form-group">
                        <label for="aiMode">Transformation Mode</label>
                        <select id="aiMode" name="mode" class="form-control" required>
                            <option value="">-- Select a mode --</option>
                            <!-- Core AI Features -->
                            <optgroup label="Core AI Features">
                                <option value="remove_background">Remove Background</option>
                                <option value="replace_background">Replace Background with Color</option>
                                <option value="auto_crop">Auto Crop</option>
                                <option value="upscale">Upscale (2x)</option>
                                <option value="enhance">Auto Enhance</option>
                                <option value="add_shadow">Add Drop Shadow</option>
                            </optgroup>
                            <!-- Generative AI (Paid Plan) -->
                            <optgroup label="Generative AI (Paid Plan Required)">
                                <option value="gen_remove">⭐ Generate Remove (Remove Objects)</option>
                                <option value="gen_replace">⭐ Generate Replace (Replace Objects)</option>
                                <option value="gen_background_replace">⭐ Generate Background Replace</option>
                                <option value="gen_recolor">⭐ Generate Recolor</option>
                                <option value="gen_restore">⭐ Generate Restore (Old Photos)</option>
                            </optgroup>
                            <!-- Color & Brightness -->
                            <optgroup label="Color & Brightness">
                                <option value="auto_brightness">Auto Brightness</option>
                                <option value="auto_color">Auto Color</option>
                                <option value="auto_contrast">Auto Contrast</option>
                                <option value="auto_enhance">Auto Enhance</option>
                                <option value="improve">Improve</option>
                                <option value="grayscale">Grayscale</option>
                                <option value="sepia">Sepia Tone</option>
                                <option value="colorize">Colorize</option>
                                <option value="vibrance">Vibrance</option>
                                <option value="saturation">Saturation</option>
                                <option value="hue">Hue Shift</option>
                                <option value="brightness">Brightness</option>
                                <option value="contrast">Contrast</option>
                                <option value="gamma">Gamma</option>
                            </optgroup>
                            <!-- Artistic Effects -->
                            <optgroup label="Artistic Effects">
                                <option value="cartoonify">Cartoonify</option>
                                <option value="oil_paint">Oil Paint</option>
                                <option value="blur">Blur</option>
                                <option value="blur_faces">Blur Faces</option>
                                <option value="pixelate">Pixelate</option>
                                <option value="pixelate_faces">Pixelate Faces</option>
                                <option value="vignette">Vignette</option>
                                <option value="tint">Tint</option>
                                <option value="outline">Outline</option>
                            </optgroup>
                            <!-- Advanced Effects -->
                            <optgroup label="Advanced Effects">
                                <option value="distort">Distort</option>
                                <option value="trim">Trim</option>
                                <option value="sharpen">Sharpen</option>
                                <option value="unsharp_mask">Unsharp Mask</option>
                                <option value="fill_light">Fill Light</option>
                                <option value="replace_color">Replace Color</option>
                                <option value="theme">Theme</option>
                                <option value="redeye">Red Eye Removal</option>
                                <option value="blackwhite">Black & White</option>
                                <option value="negate">Negate</option>
                            </optgroup>
                        </select>
                    </div>
                    <small class="text-muted d-block mb-3">⭐ = Requires Cloudinary Paid Plan</small>

                    <div class="form-group" id="bgColorGroup" style="display: none;">
                        <label for="bgColor">Background Color</label>
                        <input type="color" id="bgColor" name="bg_color" class="form-control" value="#ffffff">
                        <small class="text-muted">Select color for background replacement</small>
                    </div>

                    <div id="aiProcessing" style="display: none;" class="alert alert-info">
                        <i class="fas fa-spinner fa-spin"></i> Processing image with AI...
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="aiApplyBtn">Apply Transformation</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentProductId = null;
    let currentMediaUuid = null;

    // Show/hide bg color input based on selected mode
    const aiModeSelect = document.getElementById('aiMode');
    const bgColorGroup = document.getElementById('bgColorGroup');
    
    aiModeSelect.addEventListener('change', function() {
        bgColorGroup.style.display = this.value === 'replace_background' ? 'block' : 'none';
    });

    // Handle AI Edit button click
    document.querySelectorAll('.ai-edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentProductId = this.dataset.productId;
            currentMediaUuid = this.dataset.mediaUuid;
            aiModeSelect.value = '';
            bgColorGroup.style.display = 'none';
        });
    });

    // Handle Apply Transformation button
    document.getElementById('aiApplyBtn').addEventListener('click', async function() {
        const mode = document.getElementById('aiMode').value;
        const bgColor = document.getElementById('bgColor').value;

        if (!mode) {
            alert('Please select a transformation mode');
            return;
        }

        if (!currentProductId || !currentMediaUuid) {
            alert('Product or media information missing');
            return;
        }

        // Show processing indicator
        document.getElementById('aiProcessing').style.display = 'block';
        document.getElementById('aiApplyBtn').disabled = true;

        try {
            const response = await fetch(
                `/admin/products/${currentProductId}/media/${currentMediaUuid}/ai-edit`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        mode: mode,
                        bg_color: bgColor
                    })
                }
            );

            const data = await response.json();

            if (response.ok && data.success) {
                // Success - reload the page to show new image
                showAlert('success', 'Image transformed successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('error', data.error || 'Failed to transform image');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while processing the image');
        } finally {
            document.getElementById('aiProcessing').style.display = 'none';
            document.getElementById('aiApplyBtn').disabled = false;
            $('#aiEditModal').modal('hide');
        }
    });

    // Helper function to show alerts
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        const container = document.querySelector('.content-header') || document.querySelector('section.content');
        if (container) {
            container.insertAdjacentHTML('beforeend', alertHTML);
        }
    }
});
</script>

@stop

@extends('adminlte::page')

@section('title', $product->name . ' Images')

@section('content_header')
    <h1>{{ $product->name }} Images</h1>
@stop
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-end align-items-center" style="gap: 0.5rem;">
                    <button type="button" id="go-back-btn" class="btn btn-light p-0 d-flex align-items-center justify-content-center"
                        style="width:32px;height:32px;border-radius:50%;" title="Back">
                        <span class="fas fa-arrow-left"></span>
                    </button>
                    <a href="{{route('products')}}" class="btn btn-light p-0 d-flex align-items-center justify-content-center"
                        style="width:32px;height:32px;border-radius:50%;" title="Done">
                        <span class="fas fa-check"></span>
                    </a>
                    <a href="{{route('products')}}"
                       class="btn btn-danger p-0 d-flex align-items-center justify-content-center"
                       style="width:32px;height:32px;border-radius:50%;font-weight:bold;border:2px solid #dc3545;"
                       title="Exit">
                        <span class="fas fa-times" style="font-size:1.2em;"></span>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <span class="text-warning">Recommended Image Size: </span><span class="text-danger">1000px x 1000px</span><br/>
                                        <span class="text-warning">Recommended Aspect Ratio: </span><span class="text-danger">1:1(Square)</span><br/>
                                        <span class="text-warning">Recommended Formats: </span><span class="text-danger">WebP</span><br/>
                                        <span class="text-warning">Max File Size: </span><span class="text-danger">< 1MB</span>
                                        <div id="dropzone">
                                            <form action="{{ route('product.image.insert', $product->id) }}" method="POST"
                                                class="dropzone" id="file-upload" enctype="multipart/form-data">
                                                @csrf
                                                <div class="fallback">
                                                    <input name="image" type="file" multiple />
                                                </div>
                                                <input type="hidden" name="alt_text" id="dz-alt-text-hidden" value="">
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </section>
@stop

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
    <style>
        .dropzone {
            background: #e3e6ff;
            border-radius: 13px;
            margin-left: auto;
            margin-right: auto;
            border: 2px dotted #1833FF;
            margin-top: 30px;
            margin-bottom: 30px;
            padding-top: 18px;
            padding-bottom: 18px;
            max-width: 1200px; /* wider */
            min-height: 300px;  /* taller */
        }

        .dz-remove {
            display: inline-block !important;
            width: 1.2em;
            height: 1.2em;

            position: absolute;
            top: 5px;
            right: 5px;
            z-index: 1000;

            font-size: 1.2em !important;
            line-height: 1.1em;

            text-align: center;
            font-weight: bold;
            border: 1px solid gray !important;
            border-radius: 1.2em;
            color: gray;
            background-color: white;
            opacity: .5;

        }

        .dz-remove:hover {
            text-decoration: none !important;
            opacity: 1;
        }

        /* Alt text input and button container */
        .dz-alt-container {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            margin-left: 0;
            margin-top: 10px;
            margin-bottom: 2px;
            gap: 6px;
        }
        .dz-alt-text {
            width: 100%;
            min-width: 120px;
            max-width: 100%;
            font-size: 0.97em;
            border-radius: 6px;
            border: 1px solid #bfc7d1;
            background: #f9fafd;
            padding: 4px 10px;
            overflow-x: auto;
            white-space: pre-wrap; /* allow multiline */
            box-shadow: 0 1px 2px rgba(24,51,255,0.04);
            transition: border-color 0.2s;
            resize: vertical; /* allow vertical resizing */
            min-height: 100px; /* more height for textarea */
        }
        .dz-alt-text:focus {
            border-color: #1833FF;
            outline: none;
            background: #fff;
        }
        .dz-save-alt {
            width: 100%;
            font-size: 0.95em;
            padding: 6px 0;
            border-radius: 5px;
            box-shadow: 0 1px 2px rgba(24,51,255,0.04);
            margin-top: 2px;
        }
        .dz-alt-text::-webkit-scrollbar {
            height: 6px;
        }
        .dz-alt-text::-webkit-scrollbar-thumb {
            background: #e3e6ff;
            border-radius: 4px;
        }
        /* Images in a single line with gap */
        .dropzone {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 24px;
            justify-content: flex-start;
            align-items: flex-start;
        }
        .dropzone .dz-preview {
            margin: 0;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 180px;
            max-width: 240px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(24,51,255,0.04);
            padding: 24px 14px 16px 14px; /* taller white box */
        }
        .dropzone .dz-image {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: auto;
            margin-right: auto;
            padding-top: 8px;
            padding-bottom: 8px;
            height: 180px; /* make image area taller */
        }
        .dropzone .dz-image img {
            max-height: 170px; /* ensure image fits nicely */
            width: auto;
            object-fit: contain;
        }
        .dz-ai-button {
            margin-top: 8px;
            width: 100%;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Initialize Dropzone
        var myDropZone = Dropzone.options.fileUpload = {
            paramName: "image",
            maxFilesize: 2,
            acceptedFiles: ".png, .jpg, .jpeg, .gif, .webp",
            addRemoveLinks: true,
            dictRemoveFile: "×",
            init: function() {
                let myDropzone = this;
                var currentImages = {!! isset($media) ? json_encode($media) : json_encode([]) !!};
                for (var key in currentImages) {
                    let mockFile = {
                        name: currentImages[key].file_name,
                        size: currentImages[key].size,
                        id: currentImages[key].uuid,
                        alt_text: currentImages[key].alt_text || ''
                    };
                    myDropzone.displayExistingFile(mockFile, currentImages[key].original_url);

                    setTimeout(function() {
                        var preview = $(mockFile.previewElement);
                        if (preview.length === 0) {
                            preview = $('.dropzone .dz-preview').last();
                        }
                        // Set data-file-id for Sortable
                        preview.attr('data-file-id', mockFile.id);
                        
                        // Add AI Edit button
                        if (preview.find('.dz-ai-edit-btn').length === 0) {
                            var aiBtn = $('<button type="button" class="btn btn-sm btn-warning dz-ai-edit-btn w-100" style="margin-top: 8px;"><i class="fas fa-magic"></i> AI Edit</button>');
                            aiBtn.attr('data-media-uuid', mockFile.id);
                            preview.append(aiBtn);
                        }
                        
                        // Alt-text box logic (untouched)
                        if (preview.find('.dz-alt-container').length === 0) {
                            var altText = mockFile.alt_text || '';
                            var altInput = $('<textarea class="form-control dz-alt-text" placeholder="Alt text"></textarea>');
                            altInput.val(altText);
                            var saveBtn = $('<button type="button" class="btn btn-sm btn-success dz-save-alt">Save Alt Text</button>');
                            saveBtn.on('click', function() {
                                $.ajax({
                                    type: "POST",
                                    url: "{{route('product.image.update',$product->id)}}",
                                    data: {
                                        '_method': 'PATCH',
                                        '_token': "{{csrf_token()}}",
                                        'id': mockFile.id,
                                        'alt_text': altInput.val()
                                    },
                                    success: function(response) {
                                        saveBtn.text('Saved').removeClass('btn-success').addClass('btn-secondary');
                                        setTimeout(function() {
                                            saveBtn.text('Save Alt Text').removeClass('btn-secondary').addClass('btn-success');
                                        }, 1000);
                                    }
                                });
                            });
                            var altContainer = $('<div class="dz-alt-container"></div>');
                            altContainer.append(altInput).append(saveBtn);
                            preview.append(altContainer);
                        }
                    }, 100);
                };

                this.on("addedfile", function(file) {
                    var preview = $(file.previewElement);
                    // Set data-file-id if available
                    if (file.id) {
                        preview.attr('data-file-id', file.id);
                    }
                    // Alt-text box for new uploads
                    preview.find('.dz-alt-text, .dz-alt-container').remove();
                    var altInput = $('<textarea class="form-control dz-alt-text" placeholder="Alt text" style="margin-top:5px;"></textarea>');
                    file.alt_text = '';
                    altInput.on('change', function() {
                        file.alt_text = $(this).val();
                    });
                    file._altInput = altInput;
                    preview.append(altInput);
                });

                this.on("success", function(file, response) {
                    var preview = $(file.previewElement);
                    // Remove only the textarea added for new uploads, not all .dz-alt-text
                    if (file._altInput) {
                        file._altInput.remove();
                        delete file._altInput;
                    }
                    preview.find('.dz-alt-container').remove();

                    // Attach the returned media uuid/id to the file object for later use
                    if (response && response.id) {
                        file.id = response.id;
                        // Set data-file-id for Sortable after upload
                        $(file.previewElement).attr('data-file-id', file.id);
                    }

                    setTimeout(function() {
                        // Add AI Edit button
                        if (preview.find('.dz-ai-edit-btn').length === 0) {
                            var aiBtn = $('<button type="button" class="btn btn-sm btn-warning dz-ai-edit-btn w-100" style="margin-top: 8px;"><i class="fas fa-magic"></i> AI Edit</button>');
                            aiBtn.attr('data-media-uuid', file.id);
                            preview.append(aiBtn);
                        }
                        
                        if (preview.find('.dz-alt-container').length === 0) {
                            var altInput = $('<textarea class="form-control dz-alt-text" placeholder="Alt text"></textarea>');
                            altInput.val(file.alt_text || '');
                            var saveBtn = $('<button type="button" class="btn btn-sm btn-success dz-save-alt">Save Alt Text</button>');
                            saveBtn.on('click', function() {
                                $.ajax({
                                    type: "POST",
                                    url: "{{route('product.image.update',$product->id)}}",
                                    data: {
                                        '_method': 'PATCH',
                                        '_token': "{{csrf_token()}}",
                                        'id': file.id,
                                        'alt_text': altInput.val()
                                    },
                                    success: function(response) {
                                        saveBtn.text('Saved').removeClass('btn-success').addClass('btn-secondary');
                                        setTimeout(function() {
                                            saveBtn.text('Save Alt Text').removeClass('btn-secondary').addClass('btn-success');
                                        }, 1000);
                                    }
                                });
                            });
                            var altContainer = $('<div class="dz-alt-container"></div>');
                            altContainer.append(altInput).append(saveBtn);
                            preview.append(altContainer);
                        }
                    }, 100);
                });
            },
            removedfile: function(file) {
                file.previewElement.remove();
                $.ajax({
                    type: "POST",
                    url: "{{route('product.image.delete',$product->id)}}",
                    data: {
                        '_method': 'DELETE',
                        '_token': "{{csrf_token()}}",
                        'dataURL': file.dataURL,
                        'id': file.id,
                    },
                    success: function(response) {
                    }
                });
            }
        };

        // Wait for DOM ready and Dropzone previews to be rendered
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var dzContainer = document.querySelector('.dropzone');
                if (dzContainer && typeof Sortable !== 'undefined') {
                    new Sortable(dzContainer, {
                        animation: 200,
                        easing: "cubic-bezier(0.23, 1, 0.32, 1)",
                        draggable: '.dz-preview',
                        handle: '.dz-preview',
                        onEnd: function () {
                            var order = [];
                            dzContainer.querySelectorAll('.dz-preview').forEach(function(el) {
                                var fileId = el.getAttribute('data-file-id');
                                if (fileId) order.push(fileId);
                            });
                            // Only send if all IDs are present
                            if (order.length === dzContainer.querySelectorAll('.dz-preview').length) {
                                $.ajax({
                                    type: "POST",
                                    url: "{{route('product.image.update',$product->id)}}",
                                    data: {
                                        '_method': 'PATCH',
                                        '_token': "{{csrf_token()}}",
                                        'order': order
                                    },
                                    success: function(response) {
                                        toastr.success('Image order updated successfully!');
                                    }
                                });
                            }
                        }
                    });
                }
            }, 500);
        });

        $('#go-back-btn').on('click', function() {
            window.history.back();
        });
    </script>

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
        // ========== AI Image Edit Functionality ==========
        $(document).ready(function() {
            let currentProductId = {{ $product->id }};
            let currentMediaUuid = null;

            // Show/hide bg color input based on selected mode
            const aiModeSelect = document.getElementById('aiMode');
            const bgColorGroup = document.getElementById('bgColorGroup');
            
            aiModeSelect.addEventListener('change', function() {
                bgColorGroup.style.display = this.value === 'replace_background' ? 'block' : 'none';
            });

            // Handle AI Edit button click (delegated to document for dynamic elements)
            $(document).on('click', '.dz-ai-edit-btn', function(e) {
                e.preventDefault();
                currentMediaUuid = $(this).data('media-uuid');
                aiModeSelect.value = '';
                bgColorGroup.style.display = 'none';
                $('#aiEditModal').modal('show');
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
                        toastr.success('Image transformed successfully!');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        toastr.error(data.error || 'Failed to transform image');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    toastr.error('An error occurred while processing the image');
                } finally {
                    document.getElementById('aiProcessing').style.display = 'none';
                    document.getElementById('aiApplyBtn').disabled = false;
                    $('#aiEditModal').modal('hide');
                }
            });
        });
    </script>
@stop
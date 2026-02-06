@extends('adminlte::page')

@section('title', $mode === 'edit' ? 'Edit Category-Brand Relation' : 'Create Category-Brand Relation')

@section('content_header')
    <h1>{{ $mode === 'edit' ? 'Edit' : 'Create' }} Category-Brand Relation</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @if ($mode === 'create')
                <form action="{{ route('category-brand.store') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="category_id">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-control select2" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="brand_id">Brand <span class="text-danger">*</span></label>
                            <select name="brand_id" id="brand_id" class="form-control select2" required>
                                <option value="">Select Brand</option>
                                {{-- Brands will be loaded by AJAX --}}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="summary">Summary</label>
                        <textarea name="summary" id="summary" class="form-control" rows="2">{{ old('summary') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                    </div>
                    <!-- Meta Title -->
                    <div class="form-group col-sm-12">
                        <label for="meta_title">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                            value="{{ old('meta_title') }}">
                        @error('meta_title')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Meta Description -->
                    <div class="form-group col-sm-12">
                        <label for="meta_description">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Create</button>
                    <a href="{{ route('category-brand.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            @else
                <form action="{{ route('category-brand.update', [$category->id, $brand->id]) }}" method="POST"
                    style="margin-bottom: 1rem;">
                    @csrf
                    @method('PATCH')
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Category</label>
                            <input type="text" class="form-control" value="{{ $category->name }}" disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Brand</label>
                            <input type="text" class="form-control" value="{{ $brand->name }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="summary">Summary</label>
                        <textarea name="summary" id="summary" class="form-control" rows="2">{{ old('summary', $pivot->summary ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="5">{{ old('description', $pivot->description ?? '') }}</textarea>
                    </div>
                    <!-- Meta Title -->
                    <div class="form-group col-sm-12">
                        <label for="meta_title">Meta Title</label>
                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                            value="{{ old('meta_title', $pivot->meta_title ?? '') }}">
                        @error('meta_title')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Meta Description -->
                    <div class="form-group col-sm-12">
                        <label for="meta_description">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $pivot->meta_description ?? '') }}</textarea>
                        @error('meta_description')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                    <a href="{{ route('category-brand.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
                <form action="{{ route('category-brand.delete', [$category->id, $brand->id]) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this relation?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete Relation</button>
                </form>
            @endif
        </div>
    </div>
@stop

@push('css')
    <style>
        .select2-container--default .select2-selection--single {
            border: 2px solid #007bff !important;
            border-radius: 4px !important;
            min-height: 38px;
        }

        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    <script>
        // TikTok embed provider
        const tiktokProvider = {
            name: 'tiktok',
            url: /^https:\/\/www\.tiktok\.com\/@[\w.-]+\/video\/(\d+)/,
            html: function(match) {
                const videoId = match[1];
                const url = match[0];
                return (
                    '<blockquote class="tiktok-embed" cite="' + url + '" data-video-id="' + videoId +
                    '" style="max-width: 605px;min-width: 325px;">' +
                    '<section>Loading...</section>' +
                    '</blockquote>'
                );
            }
        };

        const facebookProvider = {
            name: 'facebook',
            url: [
                /^https:\/\/www\.facebook\.com\/.*\/videos\/(\d+)/, // normal videos
                /^https:\/\/www\.facebook\.com\/watch\?v=(\d+)/, // watch?v=123
                /^https:\/\/www\.facebook\.com\/watch\/live\/\?ref=.*&v=(\d+)/, // live videos
                /^https:\/\/www\.facebook\.com\/reel\/(\d+)/ // reels
            ],
            html: function(match) {
                const videoId = match[1];
                const fallbackPage = 'themobilemandunepal'; // your FB page username
                const embedUrl = `https://www.facebook.com/${fallbackPage}/videos/${videoId}/`;
                console.log('Facebook Embed URL:', embedUrl);
                return `
            <div class="fb-video"
                data-href="${embedUrl}"
                data-width="500"
                data-show-text="false"></div>
        `;
            }
        };

        const youtubeProvider = {
            name: 'youtube',
            url: [
                /^(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([^&]+)$/,
                /^(?:https?:\/\/)?youtu\.be\/([^?&]+)$/
            ],
            html: match => {
                const videoId = match[1];
                return `<iframe width="560" height="315" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen></iframe>`;
            }
        };

        const vimoProvider = {
            name: 'vimeo',
            url: [
                /^(?:https?:\/\/)?(?:www\.)?vimeo\.com\/(\d+)$/,
                /^(?:https?:\/\/)?vimeo\.com\/channels\/[^/]+\/(\d+)$/
            ],
            html: match => {
                const videoId = match[1];
                return `<iframe src="https://player.vimeo.com/video/${videoId}" width="640" height="360" frameborder="0" allowfullscreen></iframe>`;
            }
        };

        ClassicEditor
            .create(document.querySelector('#description'), {
                ckfinder: {
                    uploadUrl: "{{ route('category-brand.image.upload') }}?_token={{ csrf_token() }}"
                },
                mediaEmbed: {
                    providers: [],
                    extraProviders: [facebookProvider, tiktokProvider, youtubeProvider, vimoProvider]
                }
            })
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    // Re-parse Facebook embeds
                    if (window.FB && typeof FB.XFBML.parse === 'function') {
                        FB.XFBML.parse();
                    }

                    // Re-parse TikTok embeds
                    if (window.tiktok && window.tiktok.embed && typeof window.tiktok.embed.load ===
                        'function') {
                        window.tiktok.embed.load();
                    }
                });
            })
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('#summary'), {
                ckfinder: {
                    uploadUrl: "{{ route('category-brand.image.upload') }}?_token={{ csrf_token() }}"
                },
                mediaEmbed: {
                    providers: [],
                    extraProviders: [facebookProvider, tiktokProvider, youtubeProvider, vimoProvider]
                }
            })
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    // Re-parse Facebook embeds
                    if (window.FB && typeof FB.XFBML.parse === 'function') {
                        FB.XFBML.parse();
                    }

                    // Re-parse TikTok embeds
                    if (window.tiktok && window.tiktok.embed && typeof window.tiktok.embed.load ===
                        'function') {
                        window.tiktok.embed.load();
                    }
                });
            })
            .catch(error => {
                console.error(error);
            });

    </script>
    <script>
    $(function () {
        $('.select2').select2();

        $('#category_id').on('change', function () {
            var categoryId = $(this).val();
            var $brand = $('#brand_id');
            $brand.html('<option value="">Loading...</option>');
            if (categoryId) {
                $.get('{{ url('admin/ajax/category-brands') }}/' + categoryId, function (data) {
                    var options = '<option value="">Select Brand</option>';
                    data.forEach(function (brand) {
                        options += '<option value="' + brand.id + '">' + brand.name + '</option>';
                    });
                    $brand.html(options).trigger('change');
                });
            } else {
                $brand.html('<option value="">Select Brand</option>');
            }
        });

        // If old value exists (validation error), trigger change to reload brands
        @if(old('category_id'))
            $('#category_id').trigger('change');
        @endif
    });
</script>
@endpush

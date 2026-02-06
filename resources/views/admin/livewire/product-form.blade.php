<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ isset($product) ? 'Edit Product' : 'Create New Product' }}</h3>
    </div>
    <form method="POST" action="{{ isset($product) ? route('product.update', $product->id) : route('product.insert') }}">
        @csrf
        @if (isset($product))
            @method('patch')
        @endif
        {{-- Add hidden redirect_url field --}}
        @if (isset($product))
            <input type="hidden" name="redirect_url"
                value="{{ old('redirect_url', request()->headers->get('referer')) }}">
        @endif
        <div class="card-body">
            <div class="row">
                <!-- Name -->
                <div class="form-group col-md-6">
                    <label for="name">Name*</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                        value="{{ isset($product) ? $product->name : old('name') }}" wire:model="name" required>
                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Price -->
                <div class="form-group col-md-6">
                    <label for="price">Price*</label>
                    <input type="text" class="form-control" id="price" name="price" placeholder="Price"
                        value="{{ isset($product) ? $product->price : old('price') }}" wire:model="price" required>
                    @error('price')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Warranty -->
                <div class="form-group col-md-6">
                    <label for="warranty">Warranty</label>
                    <input type="text" class="form-control" id="warranty" name="warranty" placeholder="Warranty"
                        value="{{ isset($product) ? $product->warranty : old('warranty') }}" wire:model="warranty">
                    @error('warranty')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @if (isset($product))
                <div class="row">
                    <!-- Slug (only when editing) -->
                    <div class="form-group col-md-6">
                        <label for="slug">Slug*</label>
                        <input type="text" class="form-control" id="slug" name="slug" placeholder="Slug"
                            value="{{ $product->slug }}" wire:model="slug" required>
                        @error('slug')
                            <div class="alert alert-danger">{{ $message ?? '' }}</div>
                        @enderror
                    </div>
                </div>
            @endif

            <div class="row">
                <!-- Short Description -->
                <div class="form-group col-md-12">
                    <label for="short_description">
                        Short Description
                        <small class="text-danger">This should contain 215 to 230 characters.</small>
                    </label>
                    <textarea id="short_description" name="short_description" class="form-control ckeditor"
                        data-upload-url="{{ route('product.image.upload') }}?_token={{ csrf_token() }}" rows="6">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                    <small id="short_description_stats" class="text-muted d-block mt-1">Characters: 0 | Words: 0</small>
                    @error('short_description')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="row">
                <!-- Description -->
                <div class="form-group col-md-12">
                    <label for="description">
                        Description
                        <small class="text-danger">This should contain a minimum of 200 words.</small>
                    </label>
                    <textarea id="description" name="description" class="form-control ckeditor"
                        data-upload-url="{{ route('product.image.upload') }}?_token={{ csrf_token() }}" rows="10">{{ old('description', $product->description ?? '') }}</textarea>
                    <small id="description_stats" class="text-muted d-block mt-1">Characters: 0 | Words: 0</small>
                    @error('description')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Brand -->
                <div class="form-group col-md-4">
                    <label for="brand_id">Brand</label>
                    <select id="brand_id" name="brand_id" class="form-control">
                        <option value="">Select a brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ (isset($product) && $product->brand_id == $brand->id) || old('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Categories -->
                <div class="form-group col-md-4">
                    <label for="category_id">Categories</label>
                    <select name="category_id[]" id="categories" class="form-control" multiple required>
                        <option value="">--select--</option>
                        @if (isset($categories))
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @if (isset($product) && $product->categories()->where('category_id', $category->id)->exists()) {{ 'selected' }} @endif>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('category_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="form-group col-md-4">
                    <label for="status">Status*</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="">Select a status</option>
                        <option value="publish"
                            {{ (isset($product) && $product->status == 'publish') || old('status') == 'publish' ? 'selected' : '' }}>
                            Publish
                        </option>
                        <option value="unpublish"
                            {{ (isset($product) && $product->status == 'unpublish') || old('status') == 'unpublish' ? 'selected' : '' }}>
                            Unpublish
                        </option>
                    </select>
                    @error('status')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <!-- In Stock -->
                <div class="form-group col-md-4">
                    <label for="in_stock">In Stock</label>
                    <select id="in_stock" name="in_stock" class="form-control">
                        <option value="1" {{ (isset($product) && $product->in_stock) || old('in_stock') == '1' ? 'selected' : '' }}>In Stock</option>
                        <option value="0" {{ (isset($product) && !$product->in_stock) || old('in_stock') == '0' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                    @error('in_stock')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Alt Text -->
                <div class="form-group col-md-6">
                    <label for="alt_text">Alt Text*</label>
                    <input type="text" class="form-control" id="alt_text" name="alt_text" placeholder="Alt Text"
                        value="{{ isset($product) ? $product->alt_text : old('alt_text') }}" wire:model="alt_text"
                        required>
                    @error('alt_text')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Keywords -->
                <div class="form-group col-md-6">
                    <label for="keywords">Keywords</label>
                    <div class="tags-container mb-2">
                        @if (is_array($keywords))
                            @foreach ($keywords as $keyword)
                                <span class="badge badge-primary">{{ $keyword }}</span>
                            @endforeach
                        @endif
                    </div>
                    <textarea class="form-control" id="keywords" name="keywords" rows="3"
                        placeholder="Enter keywords separated by commas">{{ is_array($keywords) ? implode(',', $keywords) : $keywords }}</textarea>
                    @error('keywords')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group text-center">
                <input id="submit" type="submit" value="{{ isset($product) ? 'Edit' : 'Create' }}"
                    class="btn btn-primary d-none" />
                <input type="hidden" name="action" id="form-action" value="save" />
                @if (isset($product))
                    <button type="submit" class="btn btn-success"
                        onclick="document.getElementById('form-action').value='save'">Save</button>
                    <button type="submit" class="btn btn-secondary"
                        onclick="document.getElementById('form-action').value='exit'">Save & Exit</button>
                @else
                    <button type="submit" class="btn btn-primary"
                        onclick="document.getElementById('form-action').value='create'">Create</button>
                @endif
            </div>
        </div>
    </form>
</div>
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const keywordsInput = document.querySelector('#keywords');
            const tagsContainer = document.querySelector('.tags-container');

            function updateTags() {
                const keywords = keywordsInput.value.split(',').map(kw => kw.trim()).filter(kw => kw);
                tagsContainer.innerHTML = '';
                keywords.forEach(keyword => {
                    const badge = document.createElement('span');
                    badge.className = 'badge badge-primary';
                    badge.textContent = keyword;
                    badge.style.marginRight = '5px';
                    badge.style.marginBottom = '5px';
                    tagsContainer.appendChild(badge);
                });
            }

            keywordsInput.addEventListener('input', function(e) {
                if (e.inputType === 'insertText' && e.data === ',') {
                    updateTags();
                }
            });

            keywordsInput.addEventListener('blur', updateTags);
        });
    </script>
@endpush

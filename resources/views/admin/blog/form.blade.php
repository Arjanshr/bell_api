@extends('adminlte::page')

@section('title', 'Blogs')

@section('content_header')
    <h1>Blogs</h1>
@stop

@section('content')


    <div class="card-body">
        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">{{ isset($blog) ? 'Edit Blog' : 'Create New Blog' }}</h3>
                            </div>
                            <form method="POST"
                                action="{{ isset($blog) ? route('blog.update', $blog->id) : route('blog.insert') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @if (isset($blog))
                                    @method('patch')
                                @endif
                                <div class="card-body row">
                                    <!-- Category -->
                                    <div class="form-group col-sm-6">
                                        <label for="blog_category_id">Category*</label>
                                        <select id="blog_category_id" name="blog_category_id" class="form-control" required>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ (isset($blog) && $blog->blog_category_id == $category->id) || old('blog_category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('blog_category_id')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <!-- Slug (only on edit) -->
                                    @if (isset($blog))
                                        <div class="form-group col-sm-6">
                                            <label for="slug">Slug*</label>
                                            <input type="text" class="form-control" id="slug" name="slug"
                                                value="{{ old('slug', $blog->slug) }}" required />
                                            @error('slug')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif
                                    <!-- Title -->
                                    <div class="form-group col-sm-12">
                                        <label for="title">Title*</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            placeholder="Title" value="{{ isset($blog) ? $blog->title : old('title') }}"
                                            required>
                                        @error('title')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <!-- Content -->
                                    <div class="form-group col-sm-12">
                                        <label for="content">Content</label>
                                        <textarea id="content" class="form-control ckeditor" data-upload-url="{{ route('blog.image.upload') }}?_token={{ csrf_token() }}"
                                            name="content">{{ isset($blog) ? $blog->content : old('content') }}</textarea>
                                        @error('content')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-row w-100">
                                        <div class="form-group col-sm-4">
                                            <label for="image">Image{{ isset($blog) ? '' : '*' }}</label>
                                            <input type="file" class="form-control" name="image" @if (!isset($blog)) required @endif />
                                            @if (isset($blog) && $blog->image)
                                                <img src="{{ asset('storage/blogs/' . $blog->image) }}" class="img-fluid img-thumbnail mt-2" style="height:100px" />
                                            @endif
                                            @error('image')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="image_alt">Image Alt Text (for SEO)</label>
                                            <input type="text" class="form-control" id="image_alt" name="image_alt" placeholder="Describe the image" value="{{ isset($blog) ? $blog->image_alt : old('image_alt') }}">
                                            @error('image_alt')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="status">Status*</label>
                                            <select id='status' name="status" class="form-control" required>
                                            <option value="">Select a status</option>
                                            <option value="publish"
                                                {{ (isset($blog) && $blog->status == 'publish') || old('status') == 'publish' ? 'selected' : '' }}>
                                                Publish
                                            </option>
                                            <option value="unpublish"
                                                {{ (isset($blog) && $blog->status == 'unpublish') || old('status') == 'unpublish' ? 'selected' : '' }}>
                                                Unpublish
                                            </option>

                                        </select>
                                        @error('status')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Meta Title -->
                                    <div class="form-group col-sm-12">
                                        <label for="meta_title">Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                                            value="{{ isset($blog) ? $blog->meta_title : old('meta_title') }}">
                                        @error('meta_title')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <!-- Meta Description -->
                                    <div class="form-group col-sm-12">
                                        <label for="meta_description">Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description">{{ isset($blog) ? $blog->meta_description : old('meta_description') }}</textarea>
                                        @error('meta_description')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-sm-12">
                                        <input id="submit" type="submit" value="{{ isset($blog) ? 'Edit' : 'Create' }}"
                                            class="btn btn-primary" />
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
@stop

@section('js')
@stop

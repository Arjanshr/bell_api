@extends('adminlte::page')

@section('title', 'General Settings')

@section('content_header')
    <h1>General Settings</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('setting.general.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="container-fluid">
                @foreach ($settings->chunk(3) as $settingChunk)
                    <div class="form-row">
                        @foreach ($settingChunk as $setting)
                            <div class="form-group col-md-4">
                                <label for="{{ $setting->key }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>

                                @switch($setting->type->value)
                                    @case('boolean')
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="{{ $setting->key }}" id="{{ $setting->key }}_true" value="1" {{ $setting->value == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="{{ $setting->key }}_true">True</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="{{ $setting->key }}" id="{{ $setting->key }}_false" value="0" {{ $setting->value == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="{{ $setting->key }}_false">False</label>
                                            </div>
                                        </div>
                                        @break

                                    @case('select')
                                        <div>
                                            @foreach ($setting->options ?? [] as $option)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="{{ $setting->key }}" id="{{ $setting->key }}_{{ $option }}" value="{{ $option }}" {{ $setting->value == $option ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="{{ $setting->key }}_{{ $option }}">{{ ucfirst($option) }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @break

                                    @case('textarea')
                                        <textarea name="{{ $setting->key }}" id="{{ $setting->key }}" class="form-control" rows="4">{{ $setting->value }}</textarea>
                                        @break

                                    @case('image')
                                        @if($setting->value)
                                            <div class="mb-2">
                                                <img src="{{ asset($setting->value) }}" alt="{{ $setting->key }}" style="max-height: 100px;">
                                            </div>
                                        @endif
                                        <input type="file" name="{{ $setting->key }}" id="{{ $setting->key }}" class="form-control-file">
                                        @break

                                    @default
                                        <input type="text" name="{{ $setting->key }}" id="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}" class="form-control">
                                @endswitch
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>

    </div>
</div>
@stop

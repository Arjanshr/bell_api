@extends('adminlte::page')

@section('title', 'Manage Settings')

@section('content_header')
    <h1>All Settings</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('setting.create') }}" class="btn btn-primary">Add New Setting</a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Options</th>
                    <th style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($settings as $setting)
                    <tr>
                        <td>{{ $setting->key }}</td>
                        <td>
                            @if ($setting->type === \App\Enums\SettingType::IMAGE)
                                <img src="{{ asset($setting->value) }}" alt="{{ $setting->key }}" style="max-height: 40px;">
                            @else
                                {{ Str::limit($setting->value, 50) }}
                            @endif
                        </td>
                        <td>{{ ucfirst($setting->type->value ?? $setting->type) }}</td>
                        <td>
                            @if(is_array($setting->options))
                                {{ implode(', ', $setting->options) }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('setting.edit', $setting) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                            <form action="{{ route('setting.delete', $setting) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this setting?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

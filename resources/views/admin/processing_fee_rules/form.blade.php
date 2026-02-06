@extends('adminlte::page')

@section('title', isset($rule) ? 'Edit Rule' : 'Add Rule')

@section('content_header')
    <h1>{{ isset($rule) ? 'Edit Processing Fee Rule' : 'Add Processing Fee Rule' }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-primary">
        <form method="POST" action="{{ isset($rule) ? route('processing-fee-rules.update', $rule->id) : route('processing-fee-rules.store') }}">
            @csrf
            @if(isset($rule))
                @method('PATCH')
            @endif
            <div class="card-body">
                <div class="form-group">
                    <label for="bank_id">Bank</label>
                    <select name="bank_id" id="bank_id" class="form-control" required>
                        <option value="">-- Select Bank --</option>
                        @foreach ($banks as $id => $name)
                            <option value="{{ $id }}" {{ (old('bank_id', $rule->bank_id ?? '') == $id) ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="type">Fee Type</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="percentage" {{ old('type', $rule->type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed" {{ old('type', $rule->type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="value">Value</label>
                    <input type="number" name="value" id="value" class="form-control" step="0.01" min="0"
                        value="{{ old('value', $rule->value ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="min_fee">Minimum Fee (for percentage)</label>
                    <input type="number" name="min_fee" id="min_fee" class="form-control" step="0.01" min="0"
                        value="{{ old('min_fee', $rule->min_fee ?? '') }}">
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('processing-fee-rules.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success">{{ isset($rule) ? 'Update' : 'Save' }}</button>
            </div>
        </form>
    </div>
</div>
@stop

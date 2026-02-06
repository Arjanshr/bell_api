@php
    $types = $types ?? \App\Enums\SettingType::cases();
@endphp

<div class="form-group">
    <label for="key">Key</label>
    <input type="text" name="key" id="key" class="form-control" value="{{ old('key', $setting->key ?? '') }}"
        required {{ isset($setting) ? 'readonly' : '' }}>
</div>

<div class="form-group">
    <label for="type">Type</label>
    <select name="type" id="type" class="form-control" required>
        @foreach ($types as $type)
            <option value="{{ $type->value }}"
                {{ old('type', $setting->type->value ?? '') === $type->value ? 'selected' : '' }}>
                {{ ucfirst($type->value) }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="options">Options (for select type only)</label>
    <input type="text" name="options" class="form-control"
        value="{{ old('options', isset($setting->options) && is_array($setting->options) ? implode(',', $setting->options) : '') }}"
        placeholder="Comma-separated values (e.g. test,live)">


    <small class="form-text text-muted">Only used for select-type settings.</small>
</div>

<div class="form-group">
    <label for="value">Value</label>
    @if (isset($setting) && $setting->type === \App\Enums\SettingType::IMAGE)
        @if ($setting->value)
            <div class="mb-2">
                <img src="{{ asset($setting->value) }}" alt="Current Image" style="max-height: 100px;">
            </div>
        @endif
        <input type="file" name="value" id="value" class="form-control-file">
    @else
        <input type="text" name="value" id="value" class="form-control"
            value="{{ old('value', $setting->value ?? '') }}">
    @endif
</div>

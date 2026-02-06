<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\SettingType;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'options'
    ];

    // Cast 'type' to SettingType enum automatically
    protected $casts = [
        'type' => SettingType::class,
        'options' => 'array',
    ];

    /**
     * Helper to get setting value by key with optional default
     */
    public static function get(string $key, $default = null)
    {
        // You can cache here if you want to optimize
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Helper to set or update setting value by key
     */
    public static function set(string $key, $value, SettingType|string $type = 'text'): self
    {
        // If $type is string, convert to enum if possible
        if (is_string($type)) {
            $type = SettingType::tryFrom($type) ?? SettingType::TEXT;
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
            ]
        );
    }
}

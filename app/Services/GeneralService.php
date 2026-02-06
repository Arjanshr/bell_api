<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GeneralService
{
    /**
     * Get all model class names from app/Models
     */
    public function getModels(string $path): array
    {
        $models = [];

        if (!File::exists($path)) {
            return $models;
        }

        foreach (File::allFiles($path) as $file) {
            // Ignore non-php files
            if ($file->getExtension() !== 'php') {
                continue;
            }

            // Get file name without extension (User, OrderItem, etc.)
            $models[] = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }

        return $models;
    }

    /**
     * Convert CamelCase to kebab-case
     * Example: OrderItem -> order-item
     */
    public function convertCamelCase(string $value): string
    {
        return Str::kebab($value);
    }

    /**
     * Pluralize a word
     * Example: user -> users
     */
    public function pluralize(int $count, string $value): string
    {
        return Str::plural($value, $count);
    }
}

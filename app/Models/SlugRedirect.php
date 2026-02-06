<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlugRedirect extends Model
{
    protected $fillable = [
        'old_slug',
        'new_slug',
    ];

    public static function resolveFinalSlug(string $slug, int $depth = 0, int $maxDepth = 10): ?string
    {
        if ($depth > $maxDepth) {
            return null;
        }

        $redirect = self::where('old_slug', $slug)->first();

        if (!$redirect) {
            return null;
        }

        $nextSlug = $redirect->new_slug;

        if ($nextSlug === $slug) {
            return null;
        }

        return self::resolveFinalSlug($nextSlug, $depth + 1, $maxDepth) ?? $nextSlug;
    }
}

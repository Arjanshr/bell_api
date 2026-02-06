<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Brand extends Model
{
    use HasFactory, LogsActivity, HasSlug;
    protected $fillable = [
        'name',
        'description',
        'image',
        'summary',
        'meta_title',
        'meta_description',
        'slug',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function checkIfHasItems()
    {
        $product_count = $this->products()->count();
        if ($product_count > 0) return true;
        return false;
    }
}

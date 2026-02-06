<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\SlugRedirect;

class Blog extends Model
{
    use HasFactory, LogsActivity, HasSlug;
    protected $fillable = [
        'title',
        'content',
        'image',
        'image_alt',
        'status',
        'blog_category_id',
        'author_id',
        'meta_title',
        'meta_description',
        'slug',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'publish');
    }

    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    protected static function booted()
    {
        static::updating(function ($blog) {
            if ($blog->isDirty('slug')) {
                SlugRedirect::create([
                    'old_slug' => $blog->getOriginal('slug'),
                    'new_slug' => $blog->slug,
                ]);
            }
        });
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Campaign extends Model
{
    use HasFactory;
    use HasSlug;
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'background_image',
        'color_theme',
        'campaign_banner',
        'has_active_period',
        'start_time',
        'end_time',
        'display_order',
        'url',
        'description',
        'meta_title',
        'meta_description',
        'banner_url',
        'slug',
        'type',
        'min_cart_value',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
        // ->doNotGenerateSlugsOnUpdate(); // Optional: comment this out if you want slug to update when name changes
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('campaign_price')
            ->withTimestamps(); // If pivot table has timestamps
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function scopeNotStarted($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->where(function ($q) {
                $q->where('has_active_period', false)
                    ->orWhere(function ($q2) {
                        $q2->where('has_active_period', true)
                            ->whereTime('start_time', '<=', now()->format('H:i:s'))
                            ->whereTime('end_time', '>', now()->format('H:i:s'));
                    });
            });
    }


    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeTimeUntilStart($query)
    {
        return $query->selectRaw('TIMESTAMPDIFF(SECOND, NOW(), start_date) AS time_until_start');
    }

    public function scopeTimeSinceStarted($query)
    {
        return $query->selectRaw('TIMESTAMPDIFF(SECOND, start_date, NOW()) AS time_since_started');
    }

    public function scopeTimeUntilExpiry($query)
    {
        return $query->selectRaw('TIMESTAMPDIFF(SECOND, NOW(), end_date) AS time_until_expiry');
    }

    public function scopeTimeSinceExpired($query)
    {
        return $query->selectRaw('TIMESTAMPDIFF(SECOND, end_date, NOW()) AS time_since_expired');
    }

    public function hasStarted(): bool
    {
        return $this->start_date <= now();
    }

    // Accessors
    public function getTimeUntilStartAttribute()
    {
        return Carbon::parse($this->start_date)->diffForHumans(now(), true);
    }

    public function getTimeSinceStartedAttribute()
    {
        return Carbon::parse($this->start_date)->diffForHumans(now(), true);
    }

    public function getTimeUntilExpiryAttribute()
    {
        return Carbon::parse($this->end_date)->diffForHumans(now(), true);
    }

    public function getTimeSinceExpiredAttribute()
    {
        return Carbon::parse($this->end_date)->diffForHumans(now(), true);
    }
}

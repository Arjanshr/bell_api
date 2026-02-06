<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class Coupon extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'code',
        'type',
        'discount',
        'max_uses',
        'uses',
        'expires_at',
        'start_datetime',
        'is_user_specific',
        'specific_type',
        'status',
    ];
    protected $casts = [
        'start_datetime' => 'datetime', // Ensure 'start_datetime' is cast to a Carbon instance
        'expires_at' => 'datetime', // Make sure 'expires_at' is cast to a Carbon instance
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,  'coupon_specifics', 'coupon_id', 'specific_id');
    }
    
    public function brands()
    {
        return $this->belongsToMany(Brand::class,  'coupon_specifics', 'coupon_id', 'specific_id');
    }
    
    public function products()
    {
        return $this->belongsToMany(Product::class,  'coupon_specifics', 'coupon_id', 'specific_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user');
    }

    public function isValidForUser($userId)
    {
        if (!$this->is_user_specific) return true;
        return $this->users()->where('user_id', $userId)->exists();
    }

    // Status badge accessor
    public function getStatusBadgeAttribute()
    {
        $now = now();
        $expires = $this->expires_at;
        $starts = $this->start_datetime;
        $isActive = $this->status;
        if ($expires && $expires < $now) {
            return 'badge-expired';
        } elseif ($starts && $starts > $now) {
            return 'badge-upcoming';
        } elseif ($expires && $expires > $now) {
            return $isActive ? 'badge-active' : 'badge-inactive';
        } else {
            return $isActive ? 'badge-active' : 'badge-inactive';
        }
    }

    // Status label accessor
    public function getStatusLabelAttribute()
    {
        $now = now();
        $expires = $this->expires_at;
        $starts = $this->start_datetime;
        $isActive = $this->status;
        if ($expires && $expires < $now) {
            return 'Expired';
        } elseif ($starts && $starts > $now) {
            return 'Upcoming';
        } elseif ($expires && $expires > $now) {
            return ucfirst($isActive ? 'active' : 'inactive');
        } else {
            return ucfirst($isActive ? 'active' : 'inactive');
        }
    }

    // Validity text accessor
    public function getValidityTextAttribute()
    {
        $now = now();
        $expires = $this->expires_at;
        $starts = $this->start_datetime;
        $isActive = $this->status;
        if ($expires && $expires < $now) {
            return "Expired " . $expires->diffForHumans($now) . " (" . $expires->format('d M, Y') . ")";
        } elseif ($starts && $starts > $now) {
            return "Starts " . $starts->diffForHumans($now) . " (" . $starts->format('d M, Y') . ")";
        } elseif ($expires && $expires > $now) {
            return "Expires " . $expires->diffForHumans($now) . " (" . $expires->format('d M, Y') . ")";
        } else {
            return $expires ? $expires->format('d M, Y') : '';
        }
    }
}

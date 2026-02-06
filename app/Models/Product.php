<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Scout\Searchable;
use App\Models\SlugRedirect;

class Product extends Model implements HasMedia
{
    use HasFactory, LogsActivity, HasSlug, InteractsWithMedia, Searchable;

    protected $fillable = [
        'brand_id',
        'name',
        'description',
        'short_description',
        'price',
        'warranty',
        'status',
        'in_stock',
        'alt_text',
        'keywords',
        'slug'
    ];

    protected $casts = [
        'in_stock' => 'boolean',
    ];

    // region: Relationships

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function specifications()
    {
        return $this->belongsToMany(Specification::class)
            ->withPivot('value');
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class)->withPivot('campaign_price');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // endregion

    // region: Scopes

    public function scopePublished($query)
    {
        return $query->where('status', 'publish');
    }

    // endregion

    // region: Attribute Accessors

    public function getAverageRating()
    {
        $confirmed_reviews = $this->reviews()->where('status', 'confirmed');
        $total_count = $confirmed_reviews->count();
        $total_rating = $confirmed_reviews->sum('rating');
        return $total_count > 0 ? $total_rating / $total_count : 0.00;
    }

    public function getPrimaryImageAttribute()
    {
        $images = $this->images->where('is_primary', 1);
        if ($images && $images->count() > 0) {
            $primary_image = $images->first()->image;
        } else {
            $image = $this->images->first();
            if ($image) {
                $primary_image = $image->image;
            } else {
                return null;
            }
        }
        return 'products/' . $primary_image;
    }

    public function getDiscountedPriceAttribute()
    {
        $campaign = $this->campaigns()->running()->first();
        return $campaign ? $campaign->pivot->campaign_price : $this->price;
    }

    public function getMetaTitleAttribute()
    {
        return 'Buy '.$this->name . ' Online at Best Price - ' . config('app.name');
    }

    public function getMetaDescriptionAttribute()
    {
        $product_name = $this->name;
        $brand_name = $this->brand->name;
        $site_name = config('app.name');

        $first_feature = $this->features->first()->feature ?? '';
        $second_feature = $this->features->skip(1)->first()->feature ?? '';

        $key_feature_1 = $this->extractKeyFeature($first_feature, 'High quality');
        $key_feature_2 = $this->extractKeyFeature($second_feature, 'Affordable price');

        // Remove trailing full stop from features
        $key_feature_1 = rtrim($key_feature_1, '.');
        $key_feature_2 = rtrim($key_feature_2, '.');

        return "Buy ". $product_name." at ".$site_name.". ". $key_feature_1.", ". $key_feature_2.". Fast delivery and best price.";
    }

    protected function extractKeyFeature($html, $fallback)
    {
        // Extract <h5> content
        if (preg_match('/<h5[^>]*>(.*?)<\/h5>/i', $html, $matches)) {
            return trim(strip_tags($matches[1]));
        }

        // Clean the HTML (remove all tags)
        $plain_text = trim(strip_tags($html));

        if ($plain_text) {
            return str_contains($plain_text, ':')
                ? trim(explode(':', $plain_text)[0])
                : $plain_text;
        }

        return $fallback;
    }

    public function getMetaKeywordsAttribute()
    {
        return $this->keywords ?? $this->name;
    }

    public function getSkuAttribute()
    {
        $brand_part = $this->brand && $this->brand->name
            ? strtoupper(substr($this->brand->name, 0, 3))
            : 'SKU';
        $id_padded = str_pad($this->id, 4, '0', STR_PAD_LEFT);
        return $brand_part . '-' . $id_padded;
    }

    public function getSoldCountAttribute()
    {
        return OrderItem::where('product_id', $this->id)
            ->whereHas('order', function ($q) {
                $q->where('status', OrderStatus::COMPLETED->value);
            })
            ->sum('quantity');
    }

    // endregion

    // region: Sluggable & Activity Log

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    // endregion

    // region: Miscellaneous

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'status' => $this->status,
            'brand_id' => $this->brand_id,
            // For Meilisearch filter: categories.id must be an array of IDs
            'categories' => $this->categories->pluck('id')->toArray(),
            'rating' => $this->getAverageRating(),
        ];
    }

    public function isPopular()
    {
        return PopularProduct::where('product_id', $this->id)->exists();
    }

    public function isNew()
    {
        return NewArraival::where('product_id', $this->id)->exists();
    }

    public function isCampaignProduct()
    {
        return $this->campaigns()
            ->where('status', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->get();
    }

    // endregion

    protected static function booted()
    {
        static::updating(function ($product) {
            if ($product->isDirty('slug')) {
                SlugRedirect::create([
                    'old_slug' => $product->getOriginal('slug'),
                    'new_slug' => $product->slug,
                ]);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductViewLog extends Model
{
    use HasFactory;

    protected $table = 'product_view_logs';

    protected $fillable = [
        'product_id',
        'user_id',
        'ip_address',
        'viewed_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    // Relationships

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

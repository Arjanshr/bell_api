<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'is_read',
        'product_id',
        'campaign_id',
        'order_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

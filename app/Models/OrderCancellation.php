<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    protected $fillable = [
        'order_id',
        'order_cancellation_category_id',
        'reason',
        'admin_id',
    ];

    public function category()
    {
        return $this->belongsTo(OrderCancellationCategory::class, 'order_cancellation_category_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}


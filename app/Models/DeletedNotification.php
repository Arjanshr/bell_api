<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedNotification extends Model
{
    protected $table = 'deleted_notifications';

    protected $fillable = [
        'user_id',
        'product_id',
        'campaign_id',
        'deleted_at',
    ];

    public $timestamps = true;
}

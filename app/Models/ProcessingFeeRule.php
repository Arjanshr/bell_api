<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessingFeeRule extends Model
{
    protected $fillable = ['bank_id', 'type', 'value', 'min_fee'];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}


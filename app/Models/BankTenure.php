<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTenure extends Model
{
    protected $fillable = [
        'bank_id',
        'months',
        'service_charge_percent',
        'min_service_charge_amount'
    ];

    protected $casts = [
        'service_charge_percent' => 'float',
        'min_service_charge_amount' => 'float',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}

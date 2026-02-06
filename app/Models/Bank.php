<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
        protected $fillable = ['name','min_emi_price'];

    public function tenures()
    {
        return $this->hasMany(BankTenure::class);
    }

    public function processingFeeRule()
    {
        return $this->hasOne(ProcessingFeeRule::class);
    }
}

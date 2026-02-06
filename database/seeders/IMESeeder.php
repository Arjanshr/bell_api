<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\BankTenure;
use App\Models\ProcessingFeeRule;
use Illuminate\Database\Seeder;

class IMESeeder extends Seeder
{
    public function run(): void
    {
        // Bank 1: Siddartha Bank
        $siddartha = Bank::create(['name' => 'Siddartha Bank']);
        BankTenure::insert([
            ['bank_id' => $siddartha->id, 'months' => 6, 'service_charge_percent' => 10.5],
            ['bank_id' => $siddartha->id, 'months' => 12, 'service_charge_percent' => 10.5],
        ]);
        ProcessingFeeRule::create([
            'bank_id' => $siddartha->id,
            'type' => 'percentage',
            'value' => 1.0,
            'min_fee' => 1000.0,
        ]);

        // Bank 2: Laxmi Sunrise Bank (No processing fee)
        $laxmi = Bank::create(['name' => 'Laxmi Sunrise Bank']);
        BankTenure::insert([
            ['bank_id' => $laxmi->id, 'months' => 6, 'service_charge_percent' => 5],
            ['bank_id' => $laxmi->id, 'months' => 12, 'service_charge_percent' => 7],
            ['bank_id' => $laxmi->id, 'months' => 18, 'service_charge_percent' => 10],
        ]);
        ProcessingFeeRule::create([
            'bank_id' => $laxmi->id,
            'type' => 'fixed',
            'value' => 0,
            'min_fee' => null,
        ]);

        // Bank 3: Sanima Bank (No processing fee)
        $sanima = Bank::create(['name' => 'Sanima Bank']);
        BankTenure::insert([
            ['bank_id' => $sanima->id, 'months' => 3, 'service_charge_percent' => 10],
            ['bank_id' => $sanima->id, 'months' => 6, 'service_charge_percent' => 10],
            ['bank_id' => $sanima->id, 'months' => 9, 'service_charge_percent' => 10],
            ['bank_id' => $sanima->id, 'months' => 12, 'service_charge_percent' => 10],
        ]);
        ProcessingFeeRule::create([
            'bank_id' => $sanima->id,
            'type' => 'fixed',
            'value' => 0,
            'min_fee' => null,
        ]);
    }
}

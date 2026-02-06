<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderCancellationCategory;

class OrderCancellationCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Out of stock',
            'Payment verification failed',
            'Fraud / suspicious activity',
            'Pricing error',
            'Customer request',
            'Logistics issue',
        ];

        foreach ($categories as $category) {
            OrderCancellationCategory::create([
                'name' => $category,
                'status' => 1,
            ]);
        }
    }
}


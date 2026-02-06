<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            ["name" => "Koshi Province", "province_code" => "NP-P1"],
            ["name" => "Madhesh Province", "province_code" => "NP-P2"],
            ["name" => "Bagmati Province", "province_code" => "NP-P3"],
            ["name" => "Gandaki Province", "province_code" => "NP-P4"],
            ["name" => "Lumbini Province", "province_code" => "NP-P5"],
            ["name" => "Karnali Province", "province_code" => "NP-P6"],
            ["name" => "Sudurpaschim Province", "province_code" => "NP-P7"],
        ];
        foreach ($provinces as $province) {
            Province::create($province);
        }
    }
}

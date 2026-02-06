<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Enums\SettingType;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'MobileMandu',
                'type' => SettingType::TEXT,
            ],
            [
                'key' => 'site_logo',
                'value' => 'images/logo.png', // relative path to your public folder
                'type' => SettingType::IMAGE,
            ],
            [
                'key' => 'site_description',
                'value' => 'Your favorite online shopping destination',
                'type' => SettingType::TEXTAREA,
            ],
            [
                'key' => 'enable_maintenance_mode',
                'value' => '0',
                'type' => SettingType::BOOLEAN,
            ],
            [
                'key' => 'default_payment_gateway',
                'value' => 'test', // or 'live'
                'type' => SettingType::SELECT,
            ],
            // Add more default settings as needed
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}

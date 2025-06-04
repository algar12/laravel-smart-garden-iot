<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        Setting::setValue('pump_command', 'PUMP_OFF', 'Remote pump control command');
        Setting::setValue('auto_mode', 'ON', 'Enable/disable automatic watering');
        Setting::setValue('moisture_threshold', '54', 'Moisture threshold for auto watering');
    }
}

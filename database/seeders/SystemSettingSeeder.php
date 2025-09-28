<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['default_currency', 'PEN'],
            ['hotel_name', 'Hotel Demo'],
            ['timezone', 'America/Lima'],
        ];

        foreach ($settings as $s) {
            SystemSetting::firstOrCreate(
                ['key' => $s[0]],
                [
                    'id' => (string) Str::uuid(),
                    'value' => $s[1],
                ]
            );
        }
    }
}

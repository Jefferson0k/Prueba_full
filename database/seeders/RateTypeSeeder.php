<?php

namespace Database\Seeders;

use App\Models\RateType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RateTypeSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['Por Hora', 'HOUR', 1],
            ['Por Día', 'DAY', 24],
            ['Por Noche', 'NIGHT', 12],
        ];

        foreach ($tipos as $t) {
            RateType::firstOrCreate(
                ['code' => $t[1]],
                [
                    'id' => (string) Str::uuid(),
                    'name' => $t[0],
                    'duration_hours' => $t[2],
                    'is_active' => true,
                ]
            );
        }
    }
}

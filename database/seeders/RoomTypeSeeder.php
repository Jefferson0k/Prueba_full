<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['Simple', '1 cama simple', 1, 30, 100, 80],
            ['Doble', '2 camas', 2, 40, 150, 100],
            ['Matrimonial', 'Cama doble', 2, 50, 180, 120],
            ['Suite', 'Suite completa', 4, 80, 300, 200],
        ];

        foreach ($tipos as $t) {
            RoomType::firstOrCreate(
                ['name' => $t[0]],
                [
                    'id' => (string) Str::uuid(),
                    'description' => $t[1],
                    'capacity' => $t[2],
                    'base_price_per_hour' => $t[3],
                    'base_price_per_day' => $t[4],
                    'base_price_per_night' => $t[5],
                    'is_active' => true,
                ]
            );
        }
    }
}

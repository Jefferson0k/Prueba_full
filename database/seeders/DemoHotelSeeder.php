<?php

namespace Database\Seeders;

use App\Models\{Branch, Floor, RoomType, Room};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoHotelSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::firstOrCreate(
            ['code' => 'BR001'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Sucursal Centro',
                'address' => 'Av. Principal 123',
                'phone' => '999-999-999',
                'email' => 'centro@hotel.com',
                'is_active' => true,
            ]
        );

        $piso1 = Floor::firstOrCreate(
            ['branch_id' => $branch->id, 'floor_number' => 1],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Piso 1',
                'description' => null,
                'is_active' => true,
            ]
        );

        $simple = RoomType::firstOrCreate(
            ['name' => 'Simple'],
            [
                'id' => (string) Str::uuid(),
                'description' => '1 cama',
                'capacity' => 2,
                'base_price_per_hour' => 30,
                'base_price_per_day' => 110,
                'base_price_per_night' => 80,
                'is_active' => true,
            ]
        );

        foreach (range(101, 105) as $num) {
            Room::firstOrCreate(
                ['floor_id' => $piso1->id, 'room_number' => (string)$num],
                [
                    'id' => (string) Str::uuid(),
                    'room_type_id' => $simple->id,
                    'status' => 'available',
                    'is_active' => true,
                ]
            );
        }
    }
}

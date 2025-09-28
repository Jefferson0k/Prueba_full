<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Floor::all()->each(function ($floor) {
            RoomType::all()->each(function ($type) use ($floor) {
                Room::factory()->count(5)->create([
                    'floor_id' => $floor->id,
                    'room_type_id' => $type->id
                ]);
            });
        });
    }
}

<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'floor_id' => Floor::factory(),
            'room_type_id' => RoomType::factory(),
            'room_number' => (string) $this->faker->unique()->numberBetween(100, 999),
            'status' => 'available',
            'is_active' => true,
        ];
    }
}

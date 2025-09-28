<?php

namespace Database\Factories;

use App\Models\Floor;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FloorFactory extends Factory
{
    protected $model = Floor::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'branch_id' => Branch::factory(),
            'name' => 'Piso ' . $this->faker->randomDigitNotNull,
            'floor_number' => $this->faker->numberBetween(1, 10),
            'description' => $this->faker->sentence,
            'is_active' => true,
        ];
    }
}

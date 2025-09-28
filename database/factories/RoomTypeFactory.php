<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoomTypeFactory extends Factory
{
    protected $model = RoomType::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->randomElement(['Simple', 'Doble', 'Matrimonial', 'Suite']),
            'description' => $this->faker->sentence,
            'capacity' => $this->faker->numberBetween(1, 4),
            'base_price_per_hour' => $this->faker->randomFloat(2, 20, 80),
            'base_price_per_day' => $this->faker->randomFloat(2, 80, 200),
            'base_price_per_night' => $this->faker->randomFloat(2, 60, 150),
            'is_active' => true,
        ];
    }
}

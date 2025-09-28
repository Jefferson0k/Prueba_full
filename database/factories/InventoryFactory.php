<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::inRandomOrder()->first()->id,
            'product_id' => Product::inRandomOrder()->first()->id,
            'current_stock' => $this->faker->numberBetween(5, 100),
            'min_stock' => $this->faker->numberBetween(1, 10),
            'max_stock' => $this->faker->numberBetween(50, 200),
            'average_cost' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}

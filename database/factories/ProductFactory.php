<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id'       => ProductCategory::factory(),
            'name'              => $this->faker->word(),
            'description'       => $this->faker->sentence(),
            'price'    => $this->faker->randomFloat(2, 1, 300),
            'stock'    => $this->faker->numberBetween(0, 1000),
            'is_active'         => true,
        ];
    }
}

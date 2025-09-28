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
            'category_id'    => ProductCategory::factory(),
            'name'           => $this->faker->word(),
            'description'    => $this->faker->sentence(),
            'purchase_price' => $this->faker->randomFloat(2, 1, 300), // precio de compra
            'sale_price'     => $this->faker->randomFloat(2, 301, 500), // precio de venta (mayor que compra)
            'unit_type'      => $this->faker->randomElement(['piece', 'bottle', 'pack', 'kg', 'liter']),
            'is_active'      => true,
        ];
    }
}

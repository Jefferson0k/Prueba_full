<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder{
    public function run(): void{
        $categories = ProductCategory::all();
        if ($categories->isEmpty()) {
            $this->command->warn('No hay categorías. Ejecuta primero ProductCategorySeeder.');
            return;
        }
        Product::factory(50)->make()->each(function ($product) use ($categories) {
            $product->category_id = $categories->random()->id;
            $product->save();
        });
    }
}

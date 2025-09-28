<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = ['Bebidas', 'Snacks', 'Limpieza', 'Otros'];

        foreach ($cats as $c) {
            ProductCategory::firstOrCreate(
                ['name' => $c],
                [
                    'id' => (string) Str::uuid(),
                    'description' => $c . ' para el hotel',
                ]
            );
        }
    }
}

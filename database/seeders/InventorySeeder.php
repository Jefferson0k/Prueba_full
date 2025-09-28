<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // Crear inventario para 50 registros
        Inventory::factory()->count(50)->create();
    }
}

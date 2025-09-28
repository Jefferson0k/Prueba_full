<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Currency::firstOrCreate(
            ['code' => 'PEN'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Sol Peruano',
                'symbol' => 'S/',
                'exchange_rate' => 1,
                'is_default' => true,
                'is_active' => true,
            ]
        );

        Currency::firstOrCreate(
            ['code' => 'USD'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Dólar Americano',
                'symbol' => '$',
                'exchange_rate' => 3.7,
                'is_default' => false,
                'is_active' => true,
            ]
        );
    }
}

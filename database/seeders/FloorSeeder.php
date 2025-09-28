<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Floor;

class FloorSeeder extends Seeder
{
    public function run(): void
    {
        Branch::all()->each(function ($branch) {
            Floor::factory()->count(5)->create([
                'branch_id' => $branch->id
            ]);
        });
    }
}

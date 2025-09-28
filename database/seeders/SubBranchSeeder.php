<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\SubBranch;

class SubBranchSeeder extends Seeder{
    public function run(): void{
        $branches = Branch::factory()->count(1)->create([
            'is_active' => true,
        ]);
        foreach ($branches as $branch) {
            SubBranch::factory()->count(1)->create([
                'branch_id' => $branch->id,
            ]);
        }
    }
}

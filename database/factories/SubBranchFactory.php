<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\SubBranch;

class SubBranchFactory extends Factory
{
    protected $model = SubBranch::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'branch_id' => Branch::inRandomOrder()->first()->id ?? null,
            'name' => 'Hotel ' . fake()->city(),
            'code' => strtoupper(Str::random(6)),
            'address' => fake()->address(),
            'phone' => fake()->optional()->phoneNumber(),
            'email' => fake()->boolean(80) ? fake()->unique()->safeEmail() : null,
            'is_active' => fake()->boolean(90),
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }
}

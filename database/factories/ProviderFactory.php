<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition(): array
    {
        return [
            'ruc'          => $this->faker->unique()->numerify('###########'), // 11 dígitos
            'razon_social' => $this->faker->company(),
            'telefono'     => $this->faker->phoneNumber(),
            'direccion'    => $this->faker->address(),
        ];
    }
}

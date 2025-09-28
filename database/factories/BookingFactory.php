<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('now', '+1 days');
        $checkOut = (clone $checkIn)->modify('+'.rand(1,3).' hours');

        return [
            'id' => (string) Str::uuid(),
            'room_id' => Room::factory(),
            'client_id' => Client::factory(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => 'pending',
            'total_amount' => $this->faker->randomFloat(2, 50, 300),
            'currency_id' => 1, // ajusta con tu seeder Currency
        ];
    }
}

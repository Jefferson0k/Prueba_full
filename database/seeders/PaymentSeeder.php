<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Booking;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        Booking::all()->each(function ($booking) {
            Payment::factory()->count(1)->create([
                'booking_id' => $booking->id
            ]);
        });
    }
}

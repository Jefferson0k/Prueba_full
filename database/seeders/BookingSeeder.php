<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Room;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        Client::all()->each(function ($client) {
            Room::inRandomOrder()->take(3)->get()->each(function ($room) use ($client) {
                Booking::factory()->create([
                    'client_id' => $client->id,
                    'room_id'   => $room->id
                ]);
            });
        });
    }
}

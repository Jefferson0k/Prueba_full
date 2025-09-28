<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function broadcastOn()
    {
        $branchId = $this->booking->room->floor->branch_id;
        return new Channel('bookings.' . $branchId);
    }

    public function broadcastAs()
    {
        return 'booking.created';
    }

    public function broadcastWith()
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'client_name' => $this->booking->client->full_name,
            'room_number' => $this->booking->room->room_number,
            'check_in' => $this->booking->check_in->toISOString(),
            'check_out' => $this->booking->check_out->toISOString(),
            'total_amount' => $this->booking->total_amount,
        ];
    }
}
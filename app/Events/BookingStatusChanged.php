<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $oldStatus;
    public $newStatus;

    public function __construct(Booking $booking, $oldStatus, $newStatus)
    {
        $this->booking = $booking;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn()
    {
        $branchId = $this->booking->room->floor->branch_id;
        return new Channel('bookings.' . $branchId);
    }

    public function broadcastAs()
    {
        return 'booking.status.changed';
    }
}

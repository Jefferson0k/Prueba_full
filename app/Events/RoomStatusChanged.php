<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $oldStatus;
    public $newStatus;

    public function __construct(Room $room, $oldStatus, $newStatus)
    {
        $this->room = $room;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn()
    {
        return new Channel('rooms.' . $this->room->floor->branch_id);
    }

    public function broadcastAs()
    {
        return 'room.status.changed';
    }

    public function broadcastWith()
    {
        return [
            'room_id' => $this->room->id,
            'room_number' => $this->room->room_number,
            'floor_name' => $this->room->floor->name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'timestamp' => now()->toISOString(),
        ];
    }
}

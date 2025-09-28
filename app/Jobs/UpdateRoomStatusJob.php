<?php

namespace App\Jobs;

use App\Models\Room;
use App\Models\RoomStatusLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateRoomStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $room;
    protected $oldStatus;
    protected $newStatus;
    protected $reason;
    protected $userId;

    public function __construct(Room $room, $oldStatus, $newStatus, $reason = null, $userId = null)
    {
        $this->room = $room;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->reason = $reason;
        $this->userId = $userId;
    }

    public function handle()
    {
        // Crear log de cambio de estado
        RoomStatusLog::create([
            'room_id' => $this->room->id,
            'previous_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'reason' => $this->reason,
            'changed_at' => now(),
            'changed_by' => $this->userId ?? auth()->id(),
        ]);

        // Notificar a otros sistemas si es necesario
        if ($this->newStatus === Room::STATUS_MAINTENANCE) {
            // Notificar al departamento de mantenimiento
            // Puedes agregar lógica de notificación aquí
        }

        if ($this->newStatus === Room::STATUS_CLEANING) {
            // Notificar al departamento de limpieza
            // Puedes agregar lógica de notificación aquí
        }
    }
}

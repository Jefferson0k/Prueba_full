<?php

namespace App\Jobs;

use App\Models\Room;
use App\Models\RoomStatusLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateRoomStatusJob implements ShouldQueue{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(
        public Room $room,
        public string $oldStatus,
        public string $newStatus,
        public ?string $reason = null,
        public ?string $userId = null,
        public ?string $bookingId = null
    ) {}
    public function handle(): void{
        // Registrar el cambio de estado en el log
        RoomStatusLog::create([
            'room_id' => $this->room->id,
            'booking_id' => $this->bookingId,
            'previous_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'reason' => $this->reason,
            'changed_at' => now(),
            'changed_by' => $this->userId,
        ]);

        // Aquí puedes agregar lógica adicional:
        // - Notificaciones al personal de limpieza
        // - Alertas si una habitación está en mantenimiento mucho tiempo
        // - Estadísticas de uso, etc.
    }
}
<?php

namespace App\Observers;

use App\Models\Room;
use App\Models\RoomStatusLog;
use Illuminate\Support\Facades\Auth;

class RoomObserver
{
    /**
     * Se ejecuta cuando una habitación se actualiza
     */
    public function updating(Room $room)
    {
        if ($room->isDirty('status')) {
            $oldStatus = $room->getOriginal('status');
            $newStatus = $room->status;

            // Registrar log de cambio de estado
            RoomStatusLog::create([
                'room_id'   => $room->id,
                'old_status'=> $oldStatus,
                'new_status'=> $newStatus,
                'reason'    => $room->status_change_reason ?? null,
                'changed_by'=> $room->status_change_user_id ?? Auth::id(),
            ]);
        }
    }

    /**
     * Se ejecuta después de guardar
     */
    public function updated(Room $room)
    {
        // Podrías disparar notificaciones, broadcast, etc.
        // Ejemplo: broadcast(new RoomStatusChanged($room));
    }
}

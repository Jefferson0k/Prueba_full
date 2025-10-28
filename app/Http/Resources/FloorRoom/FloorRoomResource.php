<?php

namespace App\Http\Resources\FloorRoom;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class FloorRoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'floor_number' => $this->floor_number,
            'description' => $this->description,
            'total_rooms' => $this->rooms->count(),
            'available_rooms' => $this->availableRooms->count(),

            'rooms' => $this->rooms->map(function ($room) {
                // Obtener reserva activa (en uso actualmente)
                $activeBooking = $room->bookings
                    ->whereIn('status', ['checked_in'])
                    ->sortByDesc('check_in')
                    ->first();

                $checkIn = null;
                $checkOut = null;
                $elapsed = null;
                $elapsedMinutes = null;
                $remainingTime = null;
                $customerName = null;

                if ($activeBooking && $activeBooking->check_in) {
                    $checkIn = Carbon::parse($activeBooking->check_in);
                    $checkOut = $activeBooking->check_out ? Carbon::parse($activeBooking->check_out) : null;

                    // Calcular tiempo transcurrido
                    $diff = now()->diff($checkIn);
                    $hours = $diff->h + ($diff->days * 24);
                    $minutes = $diff->i;
                    $seconds = $diff->s;
                    $elapsed = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                    $elapsedMinutes = ($hours * 60) + $minutes;

                    // Calcular tiempo restante (si hay check_out)
                    if ($checkOut) {
                        $remainingDiff = now()->diff($checkOut, false);
                        $remainingTime = $remainingDiff->invert
                            ? '-' . sprintf('%02d:%02d:%02d', $remainingDiff->h + ($remainingDiff->days * 24), $remainingDiff->i, $remainingDiff->s)
                            : sprintf('%02d:%02d:%02d', $remainingDiff->h + ($remainingDiff->days * 24), $remainingDiff->i, $remainingDiff->s);
                    }

                    // Buscar último pago completado y obtener cliente
                    $lastPayment = $activeBooking->payments()
                        ->where('status', 'completed')
                        ->latest('payment_date')
                        ->first();

                    if ($lastPayment && $lastPayment->booking && $lastPayment->booking->customer) {
                        $customerName = $lastPayment->booking->customer->name;
                    }
                }

                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'name' => $room->name,
                    'status' => $room->status,
                    'is_active' => $room->is_active,
                    'room_type' => $room->roomType?->name,

                    // Tiempos
                    'check_in' => $checkIn?->toDateTimeString(),
                    'check_out' => $checkOut?->toDateTimeString(),
                    'elapsed_time' => $elapsed,
                    'elapsed_minutes' => $elapsedMinutes,
                    'remaining_time' => $remainingTime,

                    // Cliente y reserva
                    'customer' => $customerName,
                    'booking_code' => $activeBooking?->booking_code,
                ];
            }),
        ];
    }
}

<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoomStatusController extends Controller
{
    /**
     * Iniciar uso de habitación (desde una reserva confirmada)
     */
    public function startRoom(Request $request, Booking $booking)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Verificar que la reserva esté confirmada
            if ($booking->status !== Booking::STATUS_CONFIRMED) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva debe estar confirmada para iniciar'
                ], 422);
            }

            // Hacer check-in
            $booking->checkIn(Auth::id());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Habitación iniciada correctamente',
                'data' => [
                    'booking' => $booking->load(['room', 'customer']),
                    'room' => $booking->room
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extender tiempo de una habitación ocupada
     */
    public function extendTime(Request $request, Booking $booking)
    {
        $request->validate([
            'additional_hours' => 'required|integer|min:1|max:24',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Verificar que esté en check-in
            if ($booking->status !== Booking::STATUS_CHECKED_IN) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se puede extender el tiempo de habitaciones ocupadas'
                ], 422);
            }

            $additionalHours = $request->additional_hours;
            $ratePerHour = $booking->rate_per_hour ?? $booking->rate_per_unit;

            // Actualizar horas y montos
            $booking->total_hours += $additionalHours;
            $booking->check_out = $booking->check_out->addHours($additionalHours);
            
            // Recalcular totales
            $additionalSubtotal = $ratePerHour * $additionalHours;
            $booking->room_subtotal += $additionalSubtotal;
            $booking->subtotal += $additionalSubtotal;
            $booking->total_amount += $additionalSubtotal;
            
            $booking->notes = ($booking->notes ?? '') . "\n[" . now() . "] Extensión: +{$additionalHours}h. " . ($request->notes ?? '');
            $booking->updated_by = Auth::id();
            $booking->save();

            // Registrar en el log de la habitación
            $booking->room->statusLogs()->create([
                'room_id' => $booking->room_id,
                'booking_id' => $booking->id,
                'previous_status' => Room::STATUS_OCCUPIED,
                'new_status' => Room::STATUS_OCCUPIED,
                'reason' => "Extensión de tiempo: +{$additionalHours} hora(s)",
                'changed_at' => now(),
                'changed_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Se extendieron {$additionalHours} hora(s) correctamente",
                'data' => [
                    'booking' => $booking->fresh(['room', 'customer']),
                    'new_total' => $booking->total_amount,
                    'new_checkout' => $booking->check_out
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al extender tiempo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Finalizar uso de habitación (check-out)
     */
    public function finishRoom(Request $request, Booking $booking)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            if ($booking->status !== Booking::STATUS_CHECKED_IN) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reserva debe estar en check-in para finalizar'
                ], 422);
            }

            // Hacer check-out (cambiará la habitación a limpieza)
            $booking->checkOut(Auth::id());

            if ($request->notes) {
                $booking->notes = ($booking->notes ?? '') . "\n[" . now() . "] Check-out: " . $request->notes;
                $booking->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Habitación finalizada. Estado: EN LIMPIEZA',
                'data' => [
                    'booking' => $booking->fresh(['room', 'customer', 'consumptions']),
                    'balance' => $booking->balance,
                    'room' => $booking->room
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar habitación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado de habitación manualmente
     */
    public function changeRoomStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,maintenance,cleaning',
            'reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $newStatus = $request->status;
            $reason = $request->reason;

            // Validaciones adicionales
            if ($newStatus === Room::STATUS_OCCUPIED) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para ocupar una habitación debe hacerse check-in desde una reserva'
                ], 422);
            }

            // Si hay una reserva activa y se quiere cambiar a disponible, advertir
            if ($newStatus === Room::STATUS_AVAILABLE && $room->hasActiveBooking()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La habitación tiene una reserva activa. Debe finalizarse primero.'
                ], 422);
            }

            $room->changeStatus($newStatus, $reason, Auth::id());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estado de habitación actualizado correctamente',
                'data' => [
                    'room' => $room->fresh(['floor', 'roomType', 'currentBooking'])
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar habitación como lista (después de limpieza)
     */
    public function markAsReady(Room $room)
    {
        try {
            DB::beginTransaction();

            if ($room->status !== Room::STATUS_CLEANING) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden marcar como listas habitaciones en limpieza'
                ], 422);
            }

            $room->changeStatus(
                Room::STATUS_AVAILABLE,
                'Limpieza completada por ' . Auth::user()->name,
                Auth::id()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Habitación marcada como disponible',
                'data' => ['room' => $room->fresh()]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de estados de una habitación
     */
    public function statusHistory(Room $room)
    {
        $logs = $room->statusLogs()
            ->with(['booking', 'changedByUser'])
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Dashboard de habitaciones en tiempo real
     */
    public function dashboard(Request $request)
    {
        // Obtener branch_id del usuario autenticado o del request
        $branchId = $request->branch_id ?? $request->user()->branch_id ?? null;

        $query = Room::with(['floor.subBranch', 'roomType', 'currentBooking.customer']);
        
        // Filtrar por sucursal si existe
        if ($branchId) {
            $query->byBranch($branchId);
        }

        $rooms = $query->get()->groupBy('status');

        $stats = [
            'available' => $rooms->get(Room::STATUS_AVAILABLE, collect())->count(),
            'occupied' => $rooms->get(Room::STATUS_OCCUPIED, collect())->count(),
            'cleaning' => $rooms->get(Room::STATUS_CLEANING, collect())->count(),
            'maintenance' => $rooms->get(Room::STATUS_MAINTENANCE, collect())->count(),
            'total' => $branchId ? Room::byBranch($branchId)->count() : Room::count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'rooms' => $rooms,
                'rooms_list' => Room::with(['floor.subBranch.branch', 'roomType', 'currentBooking.customer'])
                    ->when($branchId, fn($q) => $q->byBranch($branchId))
                    ->get()
            ]
        ]);
    }
}
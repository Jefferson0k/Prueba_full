<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class BookingObserver
{
    public function created(Booking $booking)
    {
        AuditLog::create([
            'auditable_type' => get_class($booking),
            'auditable_id' => $booking->id,
            'event' => 'created',
            'new_values' => $booking->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => Auth::id(),
        ]);
    }

    public function updated(Booking $booking)
    {
        if ($booking->isDirty()) {
            AuditLog::create([
                'auditable_type' => get_class($booking),
                'auditable_id' => $booking->id,
                'event' => 'updated',
                'old_values' => $booking->getOriginal(),
                'new_values' => $booking->getDirty(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_id' => Auth::id(),
            ]);
        }
    }

    public function deleted(Booking $booking)
    {
        AuditLog::create([
            'auditable_type' => get_class($booking),
            'auditable_id' => $booking->id,
            'event' => 'deleted',
            'old_values' => $booking->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => Auth::id(),
        ]);
    }
}

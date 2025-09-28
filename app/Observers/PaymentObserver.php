<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class PaymentObserver
{
    public function created(Payment $payment)
    {
        AuditLog::create([
            'auditable_type' => get_class($payment),
            'auditable_id' => $payment->id,
            'event' => 'created',
            'new_values' => $payment->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => Auth::id(),
        ]);
    }

    public function updated(Payment $payment)
    {
        if ($payment->isDirty()) {
            AuditLog::create([
                'auditable_type' => get_class($payment),
                'auditable_id' => $payment->id,
                'event' => 'updated',
                'old_values' => $payment->getOriginal(),
                'new_values' => $payment->getDirty(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_id' => Auth::id(),
            ]);
        }
    }
}
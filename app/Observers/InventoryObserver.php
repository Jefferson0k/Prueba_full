<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class InventoryObserver
{
    public function updated(Inventory $inventory)
    {
        if ($inventory->isDirty('current_stock')) {
            AuditLog::create([
                'auditable_type' => get_class($inventory),
                'auditable_id' => $inventory->id,
                'event' => 'stock_changed',
                'old_values' => ['current_stock' => $inventory->getOriginal('current_stock')],
                'new_values' => ['current_stock' => $inventory->current_stock],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_id' => Auth::id(),
            ]);
        }
    }
}
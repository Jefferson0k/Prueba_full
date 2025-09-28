<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use App\Traits\GeneratesCode;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Branch extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, GeneratesCode, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'deleted_at'  => 'datetime',
    ];

    // ─── Relaciones ─────────────────────────────────────────────────────────────
    public function subBranches()
    {
        return $this->hasMany(SubBranch::class, 'branch_id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function customPrices()
    {
        return $this->hasMany(BranchRoomTypePrice::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Cargar sucursales con sus locales (subbranches) y habitaciones activas
     */
    public function scopeWithActiveRooms($query)
    {
        return $query->with(['subBranches' => function ($q) {
            $q->withActiveRooms();
        }]);
    }

    // ─── Accessors ─────────────────────────────────────────────────────────────
    public function getTotalRoomsAttribute()
    {
        return $this->subBranches()
            ->withCount('rooms')
            ->get()
            ->sum('rooms_count');
    }

    public function getAvailableRoomsCountAttribute()
    {
        return $this->subBranches()
            ->withCount(['rooms as available_rooms_count' => function ($q) {
                $q->where('status', 'available')->where('is_active', true);
            }])
            ->get()
            ->sum('available_rooms_count');
    }

    public function getOccupiedRoomsCountAttribute()
    {
        return $this->subBranches()
            ->withCount(['rooms as occupied_rooms_count' => function ($q) {
                $q->where('status', 'occupied');
            }])
            ->get()
            ->sum('occupied_rooms_count');
    }
}

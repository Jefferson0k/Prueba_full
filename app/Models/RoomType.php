<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class RoomType extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name', 'description', 'capacity', 'base_price_per_hour', 
        'base_price_per_day', 'base_price_per_night', 'is_active'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'base_price_per_hour' => 'decimal:2',
        'base_price_per_day' => 'decimal:2',
        'base_price_per_night' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function customPrices()
    {
        return $this->hasMany(BranchRoomTypePrice::class);
    }

    // Método para obtener precio por sucursal y tipo de tarifa
    public function getPriceForBranch(Branch $branch, RateType $rateType)
    {
        $customPrice = $this->customPrices()
            ->where('branch_id', $branch->id)
            ->where('rate_type_id', $rateType->id)
            ->where('is_active', true)
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            })
            ->first();

        if ($customPrice) {
            return $customPrice->price;
        }

        // Precio base según el tipo de tarifa
        return match($rateType->code) {
            'HOUR' => $this->base_price_per_hour,
            'DAY' => $this->base_price_per_day,
            'NIGHT' => $this->base_price_per_night,
            default => $this->base_price_per_hour
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
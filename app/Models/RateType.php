<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class RateType extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name', 'code', 'duration_hours', 'is_active'
    ];

    protected $casts = [
        'duration_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    const CODE_HOUR = 'HOUR';
    const CODE_DAY = 'DAY';
    const CODE_NIGHT = 'NIGHT';

    // Relaciones
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function customPrices()
    {
        return $this->hasMany(BranchRoomTypePrice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculateTotalHours($checkIn, $checkOut)
    {
        $diffInHours = $checkIn->diffInHours($checkOut);
        
        return match($this->code) {
            self::CODE_HOUR => max(1, $diffInHours),
            self::CODE_DAY => max(1, ceil($diffInHours / 24)),
            self::CODE_NIGHT => 1,
            default => $diffInHours
        };
    }
}

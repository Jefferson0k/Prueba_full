<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchRoomTypePrice extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields;

    protected $fillable = [
        'branch_id', 'room_type_id', 'rate_type_id', 'price',
        'effective_from', 'effective_to', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function rateType()
    {
        return $this->belongsTo(RateType::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEffective($query, $date = null)
    {
        $date = $date ?? today();
        return $query->where('effective_from', '<=', $date)
                    ->where(function ($q) use ($date) {
                        $q->whereNull('effective_to')->orWhere('effective_to', '>=', $date);
                    });
    }

    public function isEffective($date = null)
    {
        $date = $date ?? today();
        return $this->effective_from <= $date && 
               ($this->effective_to === null || $this->effective_to >= $date) &&
               $this->is_active;
    }
}

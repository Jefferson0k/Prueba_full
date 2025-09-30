<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasAuditFields;
use \OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MovementDetail extends Model{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, Auditable;
    protected $fillable = [
        'movement_id',
        'product_id',
        'unit_price',
        'boxes',
        'units_per_box',
        'extra_units',
        'expiry_date',
    ];
    public function movement(): BelongsTo{
        return $this->belongsTo(Movement::class);
    }
    public function product(): BelongsTo{
        return $this->belongsTo(Product::class);
    }
    public function getTotalUnitsAttribute(): int{
        return ($this->boxes * $this->units_per_box) + $this->extra_units;
    }
}

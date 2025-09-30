<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasAuditFields;
use \OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Kardex extends Model{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, Auditable;
    protected $fillable = [
        'product_id',
        'sub_branch_id',
        'movement_id',
        'sale_id',
        'precio_total',
        'SAnteriorCaja',
        'SAnteriorFraccion',
        'cantidadCaja',
        'cantidadFraccion',
        'SParcialCaja',
        'SParcialFraccion',
        'movement_type',
        'movement_category',
        'estado',
    ];

    // Relaciones
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function subBranch(): BelongsTo
    {
        return $this->belongsTo(SubBranch::class);
    }

    public function movement(): BelongsTo
    {
        return $this->belongsTo(Movement::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    // Métodos auxiliares
    public function isEntrada(): bool
    {
        return $this->movement_type === 'entrada';
    }

    public function isSalida(): bool
    {
        return $this->movement_type === 'salida';
    }
}

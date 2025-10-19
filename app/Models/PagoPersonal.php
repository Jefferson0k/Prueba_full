<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoPersonal extends Model
{
    use HasFactory;

    protected $table = 'pagos_personal';

    protected $fillable = [
        'user_id',
        'sub_branch_id',
        'monto',
        'fecha_pago',
        'periodo',
        'tipo_pago',
        'metodo_pago',
        'concepto',
        'comprobante',
        'estado',
        'registrado_por'
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2'
    ];

    // Relación con el empleado
    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con la sucursal
    public function sucursal()
    {
        return $this->belongsTo(SubBranch::class, 'sub_branch_id');
    }

    // Relación con quien registró el pago
    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // Accesor para formato de monto
    public function getMontoFormateadoAttribute()
    {
        return 'S/ ' . number_format($this->monto, 2);
    }

    // Scope para filtrar por sucursal
    public function scopePorSucursal($query, $subBranchId)
    {
        return $query->where('sub_branch_id', $subBranchId);
    }

    // Scope para filtrar por estado
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditFields;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Support\Facades\Auth;

class MovementDetail extends Model implements AuditableContract
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, Auditable;

    protected $fillable = [
        'movement_id',
        'product_id',
        'unit_price',
        'boxes',
        'units_per_box',
        'expiry_date',
        'total_price',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    // ────────────────────────────
    // 🔗 Relaciones
    // ────────────────────────────
    public function movement() {
        return $this->belongsTo(Movement::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function kardexEntries() {
        return $this->hasMany(Kardex::class, 'movement_detail_id');
    }

    // ────────────────────────────
    // ⚙️ Eventos automáticos
    // ────────────────────────────
    protected static function booted()
    {
        // Antes de guardar — calcula total y asigna usuario
        static::saving(function ($model) {
            $units = ($model->boxes ?? 0) * ($model->units_per_box ?? 1);
            $model->total_price = ($model->unit_price ?? 0) * $units;

            if (Auth::check()) {
                $model->created_by = $model->created_by ?? Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        // Después de crear — actualiza stock y crea Kardex
        static::created(function ($detail) {
            $user = Auth::user();
            if (!$user) return;

            $subBranchId = $user->sub_branch_id ?? null;
            if (!$subBranchId) return;

            // Buscar producto en la sucursal del usuario
            $subBranchProduct = SubBranchProduct::where('sub_branch_id', $subBranchId)
                ->where('product_id', $detail->product_id)
                ->first();

            // Si no existe, crear registro inicial de inventario
            if (!$subBranchProduct) {
                $subBranchProduct = SubBranchProduct::create([
                    'sub_branch_id'      => $subBranchId,
                    'product_id'         => $detail->product_id,
                    'packages_in_stock'  => 0,
                    'units_per_package'  => $detail->units_per_box,
                    'current_stock'      => 0,
                    'min_stock'          => 0,
                    'max_stock'          => 0,
                    'is_fractionable'    => true,
                    'is_active'          => true,
                    'created_by'         => $user->id,
                    'updated_by'         => $user->id,
                ]);
            }

            // Calcular totales de entrada
            $totalFracciones = ($detail->boxes * $detail->units_per_box);
            $cajas = intdiv($totalFracciones, $subBranchProduct->units_per_package);
            $fracciones = $totalFracciones % $subBranchProduct->units_per_package;

            // Guardar stock anterior
            $SAnteriorCaja = $subBranchProduct->packages_in_stock;
            $SAnteriorFraccion = $subBranchProduct->current_stock % $subBranchProduct->units_per_package;

            // Actualizar stock
            $subBranchProduct->packages_in_stock += $cajas;
            $subBranchProduct->current_stock += $totalFracciones;
            $subBranchProduct->updated_by = $user->id;
            $subBranchProduct->save();

            // Registrar movimiento en Kardex
            Kardex::create([
                'product_id'         => $detail->product_id,
                'sub_branch_id'      => $subBranchProduct->sub_branch_id,
                'movement_detail_id' => $detail->id,
                'precio_total'       => $detail->total_price,
                'SAnteriorCaja'      => $SAnteriorCaja,
                'SAnteriorFraccion'  => $SAnteriorFraccion,
                'cantidadCaja'       => $cajas,
                'cantidadFraccion'   => $fracciones,
                'SParcialCaja'       => $subBranchProduct->packages_in_stock,
                'SParcialFraccion'   => $subBranchProduct->current_stock % $subBranchProduct->units_per_package,
                'movement_type'      => 'entrada',
                'movement_category'  => 'compra',
                'estado'             => 1,
                'created_by'         => $user->id,
                'updated_by'         => $user->id,
            ]);
        });
    }

    // ────────────────────────────
    // 📦 Método auxiliar (si se requiere fuera del evento)
    // ────────────────────────────
    public function addStockFromMovement(int $totalFracciones, string $subBranchId, int $userId): void
    {
        $subBranchProduct = SubBranchProduct::where('sub_branch_id', $subBranchId)
            ->where('product_id', $this->product_id)
            ->first();

        if ($subBranchProduct) {
            $cajas = intdiv($totalFracciones, $subBranchProduct->units_per_package);
            $subBranchProduct->packages_in_stock += $cajas;
            $subBranchProduct->current_stock += $totalFracciones;
            $subBranchProduct->updated_by = $userId;
            $subBranchProduct->save();
        }
    }
}

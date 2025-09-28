<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use App\Events\LowStockAlert;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Inventory extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields;

    protected $fillable = [
        'branch_id', 'product_id', 'current_stock', 'min_stock', 'max_stock', 'average_cost'
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'average_cost' => 'decimal:2',
    ];

    // Relaciones
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'product_id', 'product_id')
            ->where('branch_id', $this->branch_id);
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }

    // Métodos de negocio
    public function isLowStock()
    {
        return $this->current_stock <= $this->min_stock;
    }

    public function isOutOfStock()
    {
        return $this->current_stock <= 0;
    }

    public function updateStock($quantity, $type = 'adjustment', $reason = null, $userId = null)
    {
        $previousStock = $this->current_stock;
        $this->current_stock = $quantity;
        $this->save();

        // Crear movimiento
        InventoryMovement::create([
            'branch_id' => $this->branch_id,
            'product_id' => $this->product_id,
            'movement_code' => 'ADJ-' . now()->format('YmdHis') . '-' . $this->id,
            'movement_type' => $type,
            'quantity' => abs($quantity - $previousStock),
            'previous_stock' => $previousStock,
            'current_stock' => $quantity,
            'reason' => $reason,
            'created_by' => $userId ?? Auth::id(),
        ]);

        // Verificar stock bajo
        if ($this->isLowStock()) {
            event(new LowStockAlert($this));
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($inventory) {
            if ($inventory->isDirty('current_stock') && $inventory->isLowStock()) {
                event(new LowStockAlert($inventory));
            }
        });
    }
}
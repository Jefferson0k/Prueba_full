<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use App\Traits\GeneratesCode;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable {
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, GeneratesCode, \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'category_id', 'code', 'name', 'description',
        'purchase_price', 'sale_price', 'unit_type', 'is_active',
    ];
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    public function category() {
        return $this->belongsTo(Categoria::class, 'category_id');
    }
    public function inventory() {
        return $this->hasMany(Inventory::class);
    }
    public function movements() {
        return $this->hasMany(InventoryMovement::class);
    }
    public function consumptions() {
        return $this->hasMany(BookingConsumption::class);
    }
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }
    public function scopeInStock($query, $branchId) {
        return $query->whereHas('inventory', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)->where('current_stock', '>', 0);
        });
    }
    public function getStockForBranch($branchId) {
        return $this->inventory()->where('branch_id', $branchId)->first()?->current_stock ?? 0;
    }
    public function isInStockForBranch($branchId, $quantity = 1) {
        return $this->getStockForBranch($branchId) >= $quantity;
    }
    protected static function boot() {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->code)) {
                $product->code = $product->generateUniqueCode('PR');
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubBranchProduct extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sub_branch_id',
        'product_id',
        'current_stock',
        'min_stock',
        'max_stock',
        'custom_sale_price',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'current_stock' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'custom_sale_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function subBranch()
    {
        return $this->belongsTo(SubBranch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= min_stock');
    }

    public function scopeBySubBranch($query, $subBranchId)
    {
        return $query->where('sub_branch_id', $subBranchId);
    }

    // Métodos auxiliares
    public function getFinalSalePrice()
    {
        return $this->custom_sale_price ?? $this->product->sale_price;
    }

    public function isLowStock()
    {
        return $this->current_stock <= $this->min_stock;
    }
}

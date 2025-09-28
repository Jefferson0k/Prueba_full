<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kardex extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sub_branch_id',
        'product_id',
        'movement_type',
        'reference_type',
        'reference_id',
        'movement_date',
        'quantity',
        'unit_cost',
        'stock_before',
        'stock_after',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
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

    public function referenceable()
    {
        return $this->morphTo('referenceable', 'reference_type', 'reference_id');
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
    public function scopeByMovementType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeBySubBranch($query, $subBranchId)
    {
        return $query->where('sub_branch_id', $subBranchId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    // Métodos auxiliares
    public function isEntry()
    {
        return in_array($this->movement_type, ['entry', 'adjustment']) && $this->quantity > 0;
    }

    public function isExit()
    {
        return in_array($this->movement_type, ['exit', 'adjustment']) && $this->quantity < 0;
    }
}

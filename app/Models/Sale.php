<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sub_branch_id',
        'customer_id',
        'sale_number',
        'sale_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relaciones
    public function subBranch()
    {
        return $this->belongsTo(SubBranch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function kardexMovements()
    {
        return $this->morphMany(Kardex::class, 'referenceable', 'reference_type', 'reference_id');
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
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Métodos auxiliares
    public function canBeCancelled()
    {
        return $this->status === 'completed' || $this->status === 'pending';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (!$sale->sale_number) {
                $sale->sale_number = static::generateSaleNumber($sale->sub_branch_id);
            }
        });
    }

    public static function generateSaleNumber($subBranchId)
    {
        $lastSale = static::where('sub_branch_id', $subBranchId)
            ->orderBy('created_at', 'desc')
            ->first();

        $number = $lastSale ? intval(substr($lastSale->sale_number, -6)) + 1 : 1;
        $subBranchCode = SubBranch::find($subBranchId)->code ?? 'SB';
        
        return $subBranchCode . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}

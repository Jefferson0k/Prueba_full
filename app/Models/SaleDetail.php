<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDetail extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function sale()
    {
        return $this->belongsTo(Sale::class);
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

    // Métodos auxiliares
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($saleDetail) {
            $saleDetail->subtotal = $saleDetail->quantity * $saleDetail->unit_price;
        });

        static::updating(function ($saleDetail) {
            $saleDetail->subtotal = $saleDetail->quantity * $saleDetail->unit_price;
        });
    }
}

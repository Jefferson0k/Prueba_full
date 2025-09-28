<?php

namespace App\Models;

use App\Traits\GeneratesCode;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InventoryMovement extends Model
{
    use HasFactory, HasUuids, GeneratesCode;

    protected $fillable = [
        'branch_id', 'product_id', 'movement_code', 'movement_type', 'quantity',
        'previous_stock', 'current_stock', 'unit_cost', 'total_cost', 'reason',
        'reference_document', 'booking_id', 'transfer_to_branch_id', 'created_by',
        'approved_by', 'approved_at'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'current_stock' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    const TYPE_ENTRY = 'entry';
    const TYPE_EXIT = 'exit';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_TRANSFER = 'transfer';

    // Relaciones
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function transferToBranch()
    {
        return $this->belongsTo(Branch::class, 'transfer_to_branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopePendingApproval($query)
    {
        return $query->whereNull('approved_at');
    }

    public function approve($userId = null)
    {
        $this->approved_by = $userId ?? Auth::id();
        $this->approved_at = now();
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($movement) {
            if (empty($movement->movement_code)) {
                $prefix = strtoupper(substr($movement->movement_type, 0, 3));
                $movement->movement_code = $movement->generateUniqueCode($prefix);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'booking_id',
        'cash_register_id',
        'payment_method_id',
        'amount',
        'operation_number',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Relación con la caja registradora
     */
    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    /**
     * Relación con el método de pago
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Relación con el usuario que creó el pago
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope para pagos por caja
     */
    public function scopeByCashRegister($query, $cashRegisterId)
    {
        return $query->where('cash_register_id', $cashRegisterId);
    }

    /**
     * Scope para pagos por método de pago
     */
    public function scopeByPaymentMethod($query, $paymentMethodId)
    {
        return $query->where('payment_method_id', $paymentMethodId);
    }

    /**
     * Scope para pagos con número de operación
     */
    public function scopeWithOperationNumber($query)
    {
        return $query->whereNotNull('operation_number');
    }

    /**
     * Verificar si el pago tiene número de operación
     */
    public function hasOperationNumber(): bool
    {
        return !empty($this->operation_number);
    }
}
<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use App\Traits\GeneratesCode;
use App\Events\PaymentReceived;
use App\Jobs\ProcessPaymentJob;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Payment extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, GeneratesCode, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'payment_code', 'booking_id', 'currency_id', 'amount', 'exchange_rate',
        'amount_base_currency', 'payment_method', 'reference', 'payment_date',
        'notes', 'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'amount_base_currency' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    const METHOD_CASH = 'cash';
    const METHOD_CARD = 'card';
    const METHOD_TRANSFER = 'transfer';
    const METHOD_CHECK = 'check';
    const METHOD_OTHER = 'other';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    // Relaciones
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', today());
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Métodos de negocio
    public function process($userId = null)
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \Exception('El pago ya fue procesado');
        }

        ProcessPaymentJob::dispatch($this, $userId);
    }

    public function complete()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();

        // Actualizar monto pagado en la reserva
        $this->booking->paid_amount += $this->amount_base_currency;
        $this->booking->save();

        event(new PaymentReceived($this));
    }

    public function refund($reason = null, $userId = null)
    {
        if ($this->status !== self::STATUS_COMPLETED) {
            throw new \Exception('Solo se pueden reembolsar pagos completados');
        }

        $this->status = self::STATUS_REFUNDED;
        $this->notes = ($this->notes ? $this->notes . ' | ' : '') . 'Reembolsado: ' . $reason;
        $this->save();

        // Actualizar monto pagado en la reserva
        $this->booking->paid_amount -= $this->amount_base_currency;
        $this->booking->save();
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            if (empty($payment->payment_code)) {
                $payment->payment_code = $payment->generateUniqueCode('PAY');
            }

            // Calcular monto en moneda base si no está establecido
            if (!$payment->amount_base_currency) {
                $baseCurrency = Currency::getDefault();
                $payment->amount_base_currency = $payment->currency->convertTo($baseCurrency, $payment->amount);
            }
        });

        static::created(function ($payment) {
            if ($payment->status === self::STATUS_COMPLETED) {
                $payment->complete();
            }
        });
    }
}
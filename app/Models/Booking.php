<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use App\Traits\GeneratesCode;
use App\Events\BookingCreated;
use App\Events\BookingStatusChanged;
use App\Jobs\ProcessBookingPaymentJob;
use App\Jobs\SendBookingNotificationJob;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Booking extends Model implements AuditableContract{

    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, GeneratesCode, Auditable;

    protected $fillable = [
        'booking_code', 'room_id', 'client_id', 'rate_type_id', 'currency_id',
        'check_in', 'check_out', 'total_hours', 'rate_per_unit', 'subtotal',
        'tax_amount', 'discount_amount', 'total_amount', 'paid_amount','customers_id',
        'status', 'notes', 'cancelled_at', 'cancellation_reason', 'cancelled_by','rate_per_hour',
        'room_subtotal','products_subtotal','tax_amount','discount_amount','total_amount','updated_by',
        'sub_branch_id'
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_hours' => 'integer',
        'rate_per_unit' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_CANCELLED = 'cancelled';

    // Relaciones
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customers_id');
    }

    public function subBranch()
    {
        return $this->belongsTo(SubBranch::class, 'sub_branch_id');
    }

    public function rateType()
    {
        return $this->belongsTo(RateType::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->latest();
    }

    public function consumptions()
    {
        return $this->hasMany(BookingConsumption::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in', today());
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->whereHas('room.floor', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
    }
    public function calculateTotals()
    {
        $this->subtotal = $this->rate_per_unit * $this->total_hours;
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
    }

    public function addConsumption(Product $product, int $quantity, $userId = null)
    {
        $consumption = $this->consumptions()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'total_price' => $product->price * $quantity,
            'consumed_at' => now(),
            'created_by' => $userId ?? Auth::id(),
        ]);

        // Actualizar inventario
        $branch = $this->room->floor->branch;
        $inventory = Inventory::where('branch_id', $branch->id)
            ->where('product_id', $product->id)
            ->first();

        if ($inventory) {
            $inventory->current_stock -= $quantity;
            $inventory->save();

            // Registrar movimiento de inventario
            InventoryMovement::create([
                'branch_id' => $branch->id,
                'product_id' => $product->id,
                'movement_code' => 'CON-' . now()->format('YmdHis') . '-' . $this->id,
                'movement_type' => 'exit',
                'quantity' => $quantity,
                'previous_stock' => $inventory->current_stock + $quantity,
                'current_stock' => $inventory->current_stock,
                'unit_cost' => $product->price,
                'total_cost' => $product->price * $quantity,
                'reason' => 'Consumo en habitación: ' . $this->room->full_name,
                'booking_id' => $this->id,
                'created_by' => $userId ?? Auth::id(),
            ]);
        }

        return $consumption;
    }

    public function checkIn($userId = null)
    {
        if ($this->status !== self::STATUS_CONFIRMED) {
            throw new \Exception('La reserva debe estar confirmada para hacer check-in');
        }

        $this->status = self::STATUS_CHECKED_IN;
        $this->check_in = now();
        $this->save();

        // Cambiar estado de la habitación
        $this->room->changeStatus(Room::STATUS_OCCUPIED, 'Check-in de reserva: ' . $this->booking_code, $userId);

        event(new BookingStatusChanged($this, self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN));
    }

    public function checkOut($userId = null)
    {
        if ($this->status !== self::STATUS_CHECKED_IN) {
            throw new \Exception('La reserva debe estar en check-in para hacer check-out');
        }

        $this->status = self::STATUS_CHECKED_OUT;
        $this->check_out = now();
        $this->save();

        // Cambiar estado de la habitación a limpieza
        $this->room->changeStatus(Room::STATUS_CLEANING, 'Check-out de reserva: ' . $this->booking_code, $userId);

        // Procesar pagos pendientes si hay consumos
        ProcessBookingPaymentJob::dispatch($this);
    }

    public function cancel($reason = null, $userId = null)
    {
        $oldStatus = $this->status;
        $this->status = self::STATUS_CANCELLED;
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->cancelled_by = $userId ?? Auth::id();
        $this->save();

        // Si estaba ocupada, liberar la habitación
        if ($oldStatus === self::STATUS_CHECKED_IN) {
            $this->room->changeStatus(Room::STATUS_AVAILABLE, 'Cancelación de reserva: ' . $this->booking_code, $userId);
        }

        event(new BookingStatusChanged($this, $oldStatus, self::STATUS_CANCELLED));
    }

    public function getBalanceAttribute()
    {
        $totalConsumptions = $this->consumptions()->sum('total_price');
        return ($this->total_amount + $totalConsumptions) - $this->paid_amount;
    }

    public function isPaid()
    {
        return $this->balance <= 0;
    }

    public function getDurationInWordsAttribute()
    {
        if ($this->rateType->code === 'HOUR') {
            return $this->total_hours . ' hora(s)';
        }
        
        $hours = $this->check_in->diffInHours($this->check_out);
        $days = intval($hours / 24);
        $remainingHours = $hours % 24;
        
        if ($days > 0) {
            return $days . ' día(s)' . ($remainingHours > 0 ? ' y ' . $remainingHours . ' hora(s)' : '');
        }
        
        return $remainingHours . ' hora(s)';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = $booking->generateUniqueCode('BK');
            }
        });

        static::created(function ($booking) {
            event(new BookingCreated($booking));
            SendBookingNotificationJob::dispatch($booking, 'created');
        });

        static::updated(function ($booking) {
            if ($booking->isDirty('status')) {
                SendBookingNotificationJob::dispatch($booking, 'status_changed');
            }
        });
    }
}
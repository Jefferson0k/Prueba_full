<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Currency extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'code', 'name', 'symbol', 'exchange_rate', 'is_default', 'is_active'
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Métodos de negocio
    public function convertTo(Currency $targetCurrency, $amount)
    {
        if ($this->id === $targetCurrency->id) {
            return $amount;
        }

        // Convertir a moneda base primero, luego a moneda objetivo
        $baseAmount = $amount / $this->exchange_rate;
        return $baseAmount * $targetCurrency->exchange_rate;
    }

    public static function getDefault()
    {
        return static::default()->first();
    }

    protected static function boot()
    {
        parent::boot();

        // Solo una moneda puede ser default
        static::saving(function ($currency) {
            if ($currency->is_default) {
                static::where('id', '!=', $currency->id)->update(['is_default' => false]);
            }
        });
    }
}
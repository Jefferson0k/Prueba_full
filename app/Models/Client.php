<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Client extends Model implements Auditable
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'document_type', 'document_number', 'first_name', 'last_name',
        'phone', 'email', 'address', 'birth_date', 'gender', 'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function bookings()
    {
        return $this->hasMany(Booking::class)->latest();
    }

    public function activeBookings()
    {
        return $this->bookings()->whereIn('status', ['confirmed', 'checked_in']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDocument($query, $type, $number)
    {
        return $query->where('document_type', $type)->where('document_number', $number);
    }

    // Métodos de negocio
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function hasActiveBooking()
    {
        return $this->activeBookings()->exists();
    }

    public function getTotalBookingsAttribute()
    {
        return $this->bookings()->count();
    }

    public function getTotalSpentAttribute()
    {
        return $this->bookings()
            ->where('status', 'checked_out')
            ->sum('total_amount');
    }
}
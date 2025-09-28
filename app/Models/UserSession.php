<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'session_token', 'ip_address', 'user_agent',
        'login_at', 'logout_at', 'last_activity', 'status'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'last_activity' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_TERMINATED = 'terminated';

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeExpired($query)
    {
        return $query->where('last_activity', '<', now()->subMinutes(config('session.lifetime')));
    }

    // Métodos de negocio
    public function terminate($reason = null)
    {
        $this->status = self::STATUS_TERMINATED;
        $this->logout_at = now();
        $this->save();
    }

    public function updateActivity()
    {
        $this->last_activity = now();
        $this->save();
    }

    public function isExpired()
    {
        return $this->last_activity < now()->subMinutes(config('session.lifetime'));
    }
}
            
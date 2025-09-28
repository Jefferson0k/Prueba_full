<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomStatusLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'room_id', 'previous_status', 'new_status', 'reason', 'changed_at', 'changed_by'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    // Relaciones
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('changed_at', '>=', now()->subDays($days));
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('new_status', $status);
    }
}
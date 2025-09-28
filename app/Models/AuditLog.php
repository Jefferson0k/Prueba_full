<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model{
    use HasFactory, HasUuids;
    protected $fillable = [
        'auditable_type', 'auditable_id', 'event', 'old_values', 
        'new_values', 'ip_address', 'user_agent', 'user_id'
    ];
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];
    public function auditable(){
        return $this->morphTo();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeForModel($query, $model){
        return $query->where('auditable_type', get_class($model))
                    ->where('auditable_id', $model->getKey());
    }
    public function scopeByUser($query, $userId){
        return $query->where('user_id', $userId);
    }
    public function scopeByEvent($query, $event){
        return $query->where('event', $event);
    }
    public function scopeRecent($query, $days = 30){
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}

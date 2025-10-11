<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'dni',
        'apellidos',
        'nacimiento',
        'email',
        'username',
        'password',
        'status',
        'restablecimiento',
        'sub_branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'username', 'status'])
            ->useLogName('usuario')
            ->logOnlyDirty();
    }

    public function isOnline(): bool
    {
        return cache()->has('user-is-online-' . $this->id);
    }

    // ─── Relaciones ───────────────────────────────
    public function subBranch()
    {
        return $this->belongsTo(SubBranch::class);
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'usuario_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use App\Traits\HasAuditFields;

class Provider extends Model implements AuditableContract
{
    use Auditable, HasAuditFields, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'ruc',
        'razon_social',
        'telefono',
        'direccion',
    ];

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
}

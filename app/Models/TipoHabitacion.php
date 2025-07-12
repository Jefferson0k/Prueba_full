<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_hora',
        'precio_noche',
        'precio_dia_completo'
    ];
    public function habitaciones(){
        return $this->hasMany(Habitacion::class);
    }
}

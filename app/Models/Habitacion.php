<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model{
    use HasFactory;
    protected $fillable = [
        'numero',
        'piso_id',
        'tipo_id',
        'estado'
    ];
    public function piso(){
        return $this->belongsTo(Piso::class);
    }
    public function tipo(){
        return $this->belongsTo(TipoHabitacion::class, 'tipo_id');
    }
    public function usos(){
        return $this->hasMany(UsoHabitacion::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsoHabitacion extends Model{
    use HasFactory;
    protected $fillable = [
        'habitacion_id',
        'cliente_id',
        'horario_id',
        'tipo_uso',
        'cantidad_horas',
        'hora_entrada',
        'hora_salida',
        'total'
    ];
    public function habitacion(){
        return $this->belongsTo(Habitacion::class);
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class);
    }
    public function horario(){
        return $this->belongsTo(Horario::class);
    }
}

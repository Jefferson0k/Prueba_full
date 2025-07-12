<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piso extends Model{
    use HasFactory;
    protected $fillable = [
        'numero',
        'descripcion'
    ];
    public function habitaciones(){
        return $this->hasMany(Habitacion::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model{
    use HasFactory;
    protected $fillable = [
        'dni',
        'nombre',
        'telefono'
    ];
    public function usos(){
        return $this->hasMany(UsoHabitacion::class);
    }
}

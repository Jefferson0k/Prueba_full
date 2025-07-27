<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventario extends Model{
    use HasFactory;
    protected $table = 'inventario';
    protected $fillable = [
        'producto_id',
        'usuario_id',
        'cantidad',
        'estado'
    ];
    public function producto(){
        return $this->belongsTo(Producto::class, 'producto_id');
    }
    public function usuario(){
        return $this->belongsTo(user::class, 'usuario_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model{
    use HasFactory;
    protected $table = 'productos';
    protected $fillable = [
        'nombre',
        'categoria_id',
        'precio_compra',
        'precio_venta',
        'estado'
    ];
    public function categoria(){
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}

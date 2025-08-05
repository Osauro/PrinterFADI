<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'venta_id',
        'tienda_id',
        'producto_id',
        'enteros',
        'unidades',
        'precio',
        'precio_venta',
        'precio_compra',
        'beneficio',
        'subtotal'
    ];

    public $timestamps = false;

    public function producto()
    {
        return $this->hasOne(Producto::class, 'id', 'producto_id')->withTrashed();
    }

    public function tienda_roducto()
    {
        return $this->hasOne(TiendaProductos::class, 'id', 'producto_id');
    }

    public function tienda()
    {
        return $this->hasOne(Tienda::class, 'id', 'tienda_id');
    }
}

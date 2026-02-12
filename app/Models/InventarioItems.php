<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventarioItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'inventario_id',
        'tienda_producto_id',
        'anterior',
        'actual',
        'falta',
        'precio_venta',
        'faltante',
        'sobra',
        'precio_compra',
        'sobrante',
    ];

    public $timestamps = false;

    public function producto()
    {
        // Relación a través de tienda_productos
        return $this->hasOneThrough(
            Producto::class,
            'tienda_productos',
            'id',           // Foreign key en tienda_productos (tienda_productos.id)
            'id',           // Foreign key en productos (productos.id)
            'tienda_producto_id', // Local key en inventario_items
            'producto_id'   // Local key en tienda_productos
        )->withTrashed();
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id', 'id');
    }

    // Accessor para obtener el producto directamente
    public function getProductoDirectoAttribute()
    {
        $tiendaProducto = DB::table('tienda_productos')
            ->where('id', $this->tienda_producto_id)
            ->first();
        
        if ($tiendaProducto) {
            return Producto::withTrashed()->find($tiendaProducto->producto_id);
        }
        
        return null;
    }
}

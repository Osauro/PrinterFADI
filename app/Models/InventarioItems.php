<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'inventario_id',
        'producto_id',
        'cantidad',
    ];

    public $timestamps = false;

    public function producto()
    {
        return $this->hasOne(Producto::class, 'id', 'producto_id')->withTrashed();
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'inventario_id', 'id');
    }
}

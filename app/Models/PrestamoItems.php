<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestamoItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'prestamo_id',
        'producto_id',
        'cantidad',
        'precio',
        'subtotal'
    ];

    public $timestamps = false;

    public function producto()
    {
        return $this->hasOne(Producto::class, 'id', 'producto_id')->withTrashed();
    }
}

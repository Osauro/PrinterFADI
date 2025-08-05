<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferenciaItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'transferencia_id',
        'producto_id',
        'enteros',
        'unidades'
    ];

    public $timestamps = false;

    public function producto()
    {
        return $this->hasOne(Producto::class, 'id', 'producto_id')->withTrashed();
    }
}

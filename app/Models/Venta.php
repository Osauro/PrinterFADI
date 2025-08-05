<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'tienda_id',
        'cliente_id',
        'total',
        'efectivo',
        'cambio',
        'credito',
        'estado',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }

    public function productos()
    {
        return $this->hasMany(VentaItems::class, 'venta_id', 'id')->pluck('producto_id')->toArray();
    }

    public function items()
    {
        return $this->hasMany(VentaItems::class, 'venta_id', 'id');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function tienda()
    {
        return $this->hasOne(Tienda::class, 'id', 'tienda_id');
    }

    public function alertaBeneficio()
    {
        $aux = false;
        foreach ($this->items as $item) {
            if ($item->beneficio <= 0) {
                $aux = true;
            }
        }
        return $aux;
    }

    public function alertaPrecio()
    {
        $aux = false;
        foreach ($this->items as $item) {
            if ($item->precio < $item->precio_venta) {
                $aux = true;
            }
        }
        return $aux;
    }

    public function fecha()
    {
        //->translatedFormat('l j F Y H:i')
        $fecha = ucwords($this->created_at->translatedFormat('l')) . ' ' . $this->created_at->translatedFormat('j') . ' de ' . ucwords($this->created_at->translatedFormat('F')) . ' de ' . $this->created_at->translatedFormat('Y') . ' a las ' . $this->created_at->translatedFormat('H:i:s');
        return $fecha;
    }
}

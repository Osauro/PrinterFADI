<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tienda extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'nombre',
        'direccion',
        'celular',
        'caja'
    ];

    protected $appends = [
        'productos',
    ];

    public function getProductosAttribute()
    {
        return $this->productos()->count('id');
    }

    public function productos()
    {
        return $this->hasMany(TiendaProductos::class, 'tienda_id', 'id')->withTrashed();
    }

    public function vendedores()
    {
        return $this->belongsToMany(User::class, 'tienda_user');
    }

    public function saldo()
    {
        $movimiento = Movimiento::where('tienda_id', $this->id)->orderBy('id', 'DESC')->first();
        if ($movimiento) {
            return $movimiento->saldo;
        } else {
            return 0;
        }

    }
}

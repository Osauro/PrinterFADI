<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'tienda_id',
        'cliente_id',
        'total',
        'efectivo',
        'online',
        'estado',
        'expired_at'
    ];

    protected $dates = ['expired_at'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }


    public function productos()
    {
        return $this->hasMany(PrestamoItems::class, 'prestamo_id', 'id')->pluck('producto_id')->toArray();
    }

    public function items()
    {
        return $this->hasMany(PrestamoItems::class, 'prestamo_id', 'id');
    }

    public function tienda()
    {
        return $this->hasOne(Tienda::class, 'id', 'tienda_id');
    }
}

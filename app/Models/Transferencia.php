<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'tienda_origen',
        'tienda_destino',
        'estado'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(TransferenciaItems::class, 'transferencia_id', 'id');
    }

    public function productos()
    {
        return $this->hasMany(TransferenciaItems::class, 'transferencia_id', 'id')->pluck('producto_id')->toArray();
    }

    public function tiendaOrigen()
    {
        return $this->hasOne(Tienda::class, 'id', 'tienda_origen');
    }

    public function tiendaDestino()
    {
        return $this->hasOne(Tienda::class, 'id', 'tienda_destino');
    }
    public function fecha()
    {
        //->translatedFormat('l j F Y H:i')
        $fecha = ucwords($this->created_at->translatedFormat('l')) . ' ' . $this->created_at->translatedFormat('j') . ' de ' . ucwords($this->created_at->translatedFormat('F')) . ' de ' . $this->created_at->translatedFormat('Y') . ' a las ' . $this->created_at->translatedFormat('H:i:s');
        return $fecha;
    }
}

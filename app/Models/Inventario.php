<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'tienda_id',
        'estado',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(InventarioItems::class, 'inventario_id', 'id');
    }

    public function tienda()
    {
        return $this->hasOne(Tienda::class, 'id', 'tienda_id');
    }
}

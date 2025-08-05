<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'categoria_id',
        'nombre',
        'image',
        'medida',
        'cantidad',
        'precio_de_compra',
        'precio_por_mayor',
        'precio_por_menor',
        'precio_de_oferta',
        'fecha_fin_oferta',
        'vencidos',
        'stock_minimo',
        'stock_control',
        'control'
    ];

    protected $casts = [
        'fecha_fin_oferta' => 'datetime',
    ];

    protected $appends = [
        'photo_url',
        'stock',
        'mis_vencidos',
        'categoria',
        'precio_ultima_compra'
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : $this->defaultPhotoUrl();
    }

    protected function defaultPhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->nombre))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getStockAttribute()
    {
        $stock = TiendaProductos::where('producto_id', $this->id)->sum('stock');
        if ($this->medida == 'Unidad') {
            return $stock . 'u';
        } else {
            return floor($stock / $this->cantidad) . strtolower($this->medida)[0] . ' - ' . $stock % $this->cantidad . 'u';
        }
    }

    public function enStock()
    {
        return TiendaProductos::where('producto_id', $this->id)->sum('stock');
    }

    public function getMisVencidosAttribute()
    {
        if ($this->medida == 'Unidad') {
            return $this->vencidos . 'u';
        } else {
            return floor($this->vencidos / $this->cantidad) . strtolower($this->medida)[0] . ' - ' . $this->vencidos % $this->cantidad . 'u';
        }
    }

    public function getCategoriaAttribute()
    {
        $categoria = $this->hasOne(Categoria::class, 'id', 'categoria_id')->pluck('nombre');
        return $categoria[0];
    }

    public function control()
    {
        $stock = TiendaProductos::where('producto_id', $this->id)->sum('stock');
        if ($stock <= $this->stock_minimo) {
            $this->stock_control = true;
            $this->save();
            return $this->stock_control;
        } else {
            $this->stock_control = false;
            $this->save();
            return $this->stock_control;
        }
    }

    public function categoria()
    {
        return $this->hasOne(Categoria::class, 'id', 'categoria_id');
    }

    public function tiendas()
    {
        return $this->hasMany(TiendaProductos::class, 'producto_id', 'id');
    }

    public function bonis()
    {
        return $this->hasOne(Boni::class, 'producto_id', 'id')->where('estado', 'Pendiente');
    }

    public function tags()
    {
        return $this->hasMany(Etiqueta::class, 'producto_id', 'id');
    }

    public function kardexes()
    {
        return $this->hasMany(Kardex::class, 'producto_id', 'id');
    }

    // Método para obtener el precio de la última compra desde kardex
    public function getPrecioUltimaCompraAttribute()
    {
        $ultimaCompra = $this->kardexes()
            ->where('entrada', '>', 0)
            ->where('precio', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return $ultimaCompra ? $ultimaCompra->precio : $this->precio_de_compra;
    }

    // Métodos para control de ofertas
    public function tieneOfertaActiva()
    {
        return $this->precio_de_oferta > 0 &&
            $this->fecha_fin_oferta &&
            $this->fecha_fin_oferta->isFuture();
    }

    public function ofertaVencida()
    {
        return $this->precio_de_oferta > 0 &&
            $this->fecha_fin_oferta &&
            $this->fecha_fin_oferta->isPast();
    }

    public function getPrecioActualAttribute()
    {
        if ($this->tieneOfertaActiva()) {
            return $this->precio_de_oferta;
        }
        return $this->precio_por_mayor;
    }

    public function getTiempoRestanteOfertaAttribute()
    {
        if (!$this->fecha_fin_oferta || !$this->tieneOfertaActiva()) {
            return null;
        }

        return $this->fecha_fin_oferta->diffForHumans();
    }

    public function getSegundosRestantesOfertaAttribute()
    {
        if (!$this->fecha_fin_oferta || !$this->tieneOfertaActiva()) {
            return 0;
        }

        return now()->diffInSeconds($this->fecha_fin_oferta, false);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    public $dias_trabajados_pendientes = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'password',
        'celular',
        'tipo',
        'image',
        'sueldo',
        'fecha_pago'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->image
                    ? Storage::url($this->image)
                    : $this->defaultPhotoUrl();
    }

    protected function defaultPhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function tiendas()
    {
        return $this->belongsToMany(Tienda::class, 'tienda_user')->orderByPivot('id', 'ASC');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}

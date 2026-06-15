<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geocerca extends Model
{
    use HasFactory;

    protected $table = 'geocercas';
    protected $primaryKey = 'id_geocerca';

    protected $fillable = [
        'id_asignacion', 'nombre', 'descripcion',
        'latitud_centro', 'longitud_centro', 'radio_metros',
        'horario_inicio', 'horario_fin', 'dias_semana', 'activo',
    ];

    protected $casts = [
        'latitud_centro' => 'float',
        'longitud_centro' => 'float',
        'radio_metros' => 'float',
        'activo' => 'boolean',
        'dias_semana' => 'string',
    ];

    public $timestamps = true;

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class, 'id_asignacion', 'id_asignacion');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_geocerca', 'id_geocerca');
    }
}

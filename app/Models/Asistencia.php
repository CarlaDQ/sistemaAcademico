<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencia';
    protected $primaryKey = 'id_asistencia';

    protected $fillable = [
        'id_estudiante', 'id_asignacion', 'id_geocerca',
        'fecha', 'hora_registro', 'estado',
        'latitud_registro', 'longitud_registro', 'distancia_metros',
        'dentro_geocerca', 'dispositivo_origen', 'justificacion_url',
        'observacion', 'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_registro' => 'datetime',
        'latitud_registro' => 'float',
        'longitud_registro' => 'float',
        'distancia_metros' => 'float',
        'dentro_geocerca' => 'boolean',
        'estado' => 'string',
        'dispositivo_origen' => 'string',
    ];

    public $timestamps = false;

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class, 'id_asignacion', 'id_asignacion');
    }

    public function geocerca()
    {
        return $this->belongsTo(Geocerca::class, 'id_geocerca', 'id_geocerca');
    }

    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por', 'id_usuario');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    protected $table = 'asignaciones';
    protected $primaryKey = 'id_asignacion';

    protected $fillable = [
        'id_docente', 'id_materia', 'id_curso', 'id_anio', 'codigo', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public $timestamps = false;

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }

    public function anioLectivo()
    {
        return $this->belongsTo(AnioLectivo::class, 'id_anio', 'id_anio');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_asignacion', 'id_asignacion');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_asignacion', 'id_asignacion');
    }

    public function geocercas()
    {
        return $this->hasMany(Geocerca::class, 'id_asignacion', 'id_asignacion');
    }
}

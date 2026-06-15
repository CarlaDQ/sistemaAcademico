<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    protected $primaryKey = 'id_curso';

    protected $fillable = [
        'id_nivel', 'id_anio', 'nombre', 'grado',
        'seccion', 'capacidad', 'activo',
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'activo' => 'boolean',
    ];

    public $timestamps = true;

    public function nivel()
    {
        return $this->belongsTo(NivelEducativo::class, 'id_nivel', 'id_nivel');
    }

    public function anioLectivo()
    {
        return $this->belongsTo(AnioLectivo::class, 'id_anio', 'id_anio');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id_curso', 'id_curso');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'id_curso', 'id_curso');
    }
}

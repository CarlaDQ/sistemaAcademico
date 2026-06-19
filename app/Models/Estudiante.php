<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    protected $primaryKey = 'id_estudiante';

    protected $fillable = [
        'id_usuario', 'id_curso', 'codigo_estudiante', 'codigo_rude',
        'fecha_nacimiento', 'genero', 'direccion_cifrada',
        'observaciones_cifradas', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_nacimiento' => 'date',
        'genero' => 'string',
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }

    public function tutores()
    {
        return $this->hasMany(EstudianteTutor::class, 'id_estudiante', 'id_estudiante');
    }

    public function padres()
    {
        return $this->belongsToMany(Padre::class, 'estudiante_tutor', 'id_estudiante', 'id_padre')
            ->withPivot('es_contacto_principal');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_estudiante', 'id_estudiante');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_estudiante', 'id_estudiante');
    }

    public function citaciones()
    {
        return $this->hasMany(Citacion::class, 'id_estudiante', 'id_estudiante');
    }

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'estudiante_materia', 'id_estudiante', 'id_materia')
            ->withTimestamps();
    }
}

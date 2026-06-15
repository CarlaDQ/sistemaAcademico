<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';
    protected $primaryKey = 'id_calificacion';

    protected $fillable = [
        'id_estudiante', 'id_asignacion', 'id_periodo', 'id_tipo_eval',
        'nota', 'nota_maxima', 'fecha_evaluacion', 'observacion',
        'ingresado_por',
    ];

    protected $casts = [
        'nota' => 'float',
        'nota_maxima' => 'float',
        'fecha_evaluacion' => 'date',
    ];

    public $timestamps = true;

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class, 'id_asignacion', 'id_asignacion');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'id_periodo', 'id_periodo');
    }

    public function tipoEvaluacion()
    {
        return $this->belongsTo(TipoEvaluacion::class, 'id_tipo_eval', 'id_tipo');
    }

    public function ingresadoPor()
    {
        return $this->belongsTo(Usuario::class, 'ingresado_por', 'id_usuario');
    }
}

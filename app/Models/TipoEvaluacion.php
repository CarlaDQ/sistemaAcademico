<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'tipos_evaluacion';
    protected $primaryKey = 'id_tipo';

    protected $fillable = [
        'nombre', 'ponderacion', 'activo',
    ];

    protected $casts = [
        'ponderacion' => 'float',
        'activo' => 'boolean',
    ];

    public $timestamps = false;

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_tipo_eval', 'id_tipo');
    }
}

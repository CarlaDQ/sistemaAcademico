<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $table = 'periodos_evaluacion';
    protected $primaryKey = 'id_periodo';

    protected $fillable = [
        'id_anio', 'nombre', 'fecha_inicio', 'fecha_fin', 'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public $timestamps = false;

    public function anioLectivo()
    {
        return $this->belongsTo(AnioLectivo::class, 'id_anio', 'id_anio');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_periodo', 'id_periodo');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnioLectivo extends Model
{
    use HasFactory;

    protected $table = 'anios_lectivos';
    protected $primaryKey = 'id_anio';

    protected $fillable = [
        'nombre', 'fecha_inicio', 'fecha_fin', 'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public $timestamps = false;

    public function cursos()
    {
        return $this->hasMany(Curso::class, 'id_anio', 'id_anio');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'id_anio', 'id_anio');
    }

    public function periodos()
    {
        return $this->hasMany(Periodo::class, 'id_anio', 'id_anio');
    }
}

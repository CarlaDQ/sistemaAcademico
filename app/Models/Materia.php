<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materias';
    protected $primaryKey = 'id_materia';

    protected $fillable = [
        'id_nivel', 'nombre', 'codigo', 'horas_semanales',
        'descripcion', 'activo',
    ];

    protected $casts = [
        'horas_semanales' => 'integer',
        'activo' => 'boolean',
    ];

    public $timestamps = true;

    public function nivel()
    {
        return $this->belongsTo(NivelEducativo::class, 'id_nivel', 'id_nivel');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'id_materia', 'id_materia');
    }
}

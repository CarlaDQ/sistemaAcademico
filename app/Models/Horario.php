<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horarios';
    protected $primaryKey = 'id_horario';

    protected $fillable = [
        'id_asignacion', 'dia_semana', 'hora_inicio', 'hora_fin', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'hora_inicio' => 'string',
        'hora_fin' => 'string',
    ];

    public $timestamps = true;

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class, 'id_asignacion', 'id_asignacion');
    }
}

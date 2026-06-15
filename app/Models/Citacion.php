<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Citacion extends Model
{
    use HasFactory;

    protected $table = 'citaciones';

    protected $primaryKey = 'id_citacion';

    protected $fillable = [
        'id_docente',
        'id_estudiante',
        'titulo',
        'mensaje',
        'tipo',
        'fecha_citacion',
        'hora_citacion',
        'lugar',
        'estado',
    ];

    protected $casts = [
        'fecha_citacion' => 'date',
    ];

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }
}

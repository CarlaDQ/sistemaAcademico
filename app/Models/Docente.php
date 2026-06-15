<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docentes';
    protected $primaryKey = 'id_docente';

    protected $fillable = [
        'id_usuario', 'codigo_docente', 'especialidad',
        'titulo_academico', 'fecha_ingreso', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_ingreso' => 'date',
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'id_docente', 'id_docente');
    }

    public function citaciones()
    {
        return $this->hasMany(Citacion::class, 'id_docente', 'id_docente');
    }
}

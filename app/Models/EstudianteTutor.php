<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteTutor extends Model
{
    use HasFactory;

    protected $table = 'estudiante_tutor';

    protected $fillable = [
        'id_estudiante', 'id_padre', 'es_contacto_principal',
    ];

    protected $casts = [
        'es_contacto_principal' => 'boolean',
    ];

    public $timestamps = false;

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }

    public function padre()
    {
        return $this->belongsTo(Padre::class, 'id_padre', 'id_padre');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Padre extends Model
{
    use HasFactory;

    protected $table = 'padres_familia';
    protected $primaryKey = 'id_padre';

    protected $fillable = [
        'id_usuario', 'parentesco', 'ocupacion',
        'documento_identidad_cifrado',
    ];

    protected $casts = [
        'parentesco' => 'string',
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function tutores()
    {
        return $this->hasMany(EstudianteTutor::class, 'id_padre', 'id_padre');
    }

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'estudiante_tutor', 'id_padre', 'id_estudiante')
            ->withPivot('es_contacto_principal');
    }
}

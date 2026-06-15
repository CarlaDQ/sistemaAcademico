<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NivelEducativo extends Model
{
    use HasFactory;

    protected $table = 'niveles_educativos';

    protected $primaryKey = 'id_nivel';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'id_nivel', 'id_nivel');
    }

    public function materias(): HasMany
    {
        return $this->hasMany(Materia::class, 'id_nivel', 'id_nivel');
    }
}

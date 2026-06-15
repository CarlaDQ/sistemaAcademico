<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';

    protected $fillable = [
        'id_usuario_destino', 'id_usuario_origen', 'titulo', 'mensaje',
        'tipo', 'canal', 'leido', 'fecha_lectura',
        'entidad_tipo', 'entidad_id',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'fecha_lectura' => 'datetime',
        'tipo' => 'string',
        'canal' => 'string',
    ];

    public $timestamps = false;

    public function usuarioDestino()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_destino', 'id_usuario');
    }

    public function usuarioOrigen()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_origen', 'id_usuario');
    }
}

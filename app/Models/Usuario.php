<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Model
{
    use HasApiTokens, HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'id_rol', 'ci', 'nombre', 'apellido', 'email_cifrado', 'email_hash',
        'password_hash', 'telefono', 'avatar_url', 'estado',
        'intentos_fallidos', 'bloqueado_hasta', 'token_recuperacion',
        'token_expira_en', 'totp_secreto', 'ultimo_acceso',
        'ip_ultimo_acceso',
    ];

    protected $hidden = [
        'password_hash', 'token_recuperacion', 'totp_secreto',
    ];

    protected $casts = [
        'intentos_fallidos' => 'integer',
        'bloqueado_hasta' => 'datetime',
        'token_expira_en' => 'datetime',
        'ultimo_acceso' => 'datetime',
        'estado' => 'string',
    ];

    public $timestamps = true;

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'id_usuario', 'id_usuario');
    }

    public function docente()
    {
        return $this->hasOne(Docente::class, 'id_usuario', 'id_usuario');
    }

    public function padre()
    {
        return $this->hasOne(Padre::class, 'id_usuario', 'id_usuario');
    }

    public function notificacionesEnviadas()
    {
        return $this->hasMany(Notificacion::class, 'id_usuario_origen', 'id_usuario');
    }

    public function notificacionesRecibidas()
    {
        return $this->hasMany(Notificacion::class, 'id_usuario_destino', 'id_usuario');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RudeIntegrationController extends Controller
{
    protected $apiBase;

    public function __construct()
    {
        $this->apiBase = url('API_SEGIP');
    }

    public function consultarSegip($ci)
    {
        $r = Http::get("{$this->apiBase}/api/segip/consultar/{$ci}");

        if (!$r->successful()) {
            return response()->json([
                'encontrado' => false,
                'error' => 'CI no encontrado en SEGIP',
            ], 404);
        }

        $data = $r->json()['consulta'];

        return response()->json([
            'encontrado' => true,
            'ci' => $data['ci'],
            'nombre' => $data['nombre'],
            'apellido_paterno' => $data['apellido_paterno'],
            'apellido_materno' => $data['apellido_materno'],
            'nombre_completo' => $data['nombre_completo'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'departamento' => $data['departamento'],
            'provincia' => $data['provincia'],
            'localidad' => $data['localidad'],
            'domicilio' => $data['domicilio'],
            'genero' => $data['genero'],
        ]);
    }

    public function registrarEnRude(Request $request, $id)
    {
        $estudiante = Estudiante::with('usuario', 'curso')->findOrFail($id);

        if ($estudiante->codigo_rude) {
            return response()->json([
                'success' => true,
                'codigo_rude' => $estudiante->codigo_rude,
                'mensaje' => 'Ya registrado en RUDE',
            ]);
        }

        $r = Http::post("{$this->apiBase}/api/integracion/registrar-desde-ci", [
            'ci' => $estudiante->usuario->ci,
            'curso' => $estudiante->curso->nombre ?? 'Sin curso',
            'gestion' => 2026,
            'unidad_educativa' => 'U.E. David Pinilla',
        ]);

        if ($r->successful()) {
            $data = $r->json();
            $estudiante->update(['codigo_rude' => $data['codigo_rude'] ?? null]);

            return response()->json([
                'success' => true,
                'codigo_rude' => $data['codigo_rude'] ?? null,
                'mensaje' => 'Registrado en RUDE correctamente',
                'datos_segip' => $data['datos_segip'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Error al registrar en RUDE',
        ], 500);
    }
}

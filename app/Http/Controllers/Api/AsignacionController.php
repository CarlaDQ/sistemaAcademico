<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\Asignacion;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $docente = Docente::where('id_usuario', $user->id_usuario)->first();
        if (!$docente) {
            return response()->json([], 200);
        }

        $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
            ->with('materia', 'curso')
            ->get();

        return response()->json($asignaciones);
    }
}

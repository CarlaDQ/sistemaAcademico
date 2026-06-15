<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Docente;
use App\Models\Asignacion;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('rol');
        $calificaciones = collect();

        if ($user->rol->nombre === 'estudiante') {
            $estudiante = Estudiante::where('id_usuario', $user->id_usuario)->first();
            if ($estudiante) {
                $calificaciones = Calificacion::where('id_estudiante', $estudiante->id_estudiante)
                    ->with('tipoEvaluacion', 'asignacion.materia', 'periodo')
                    ->get();
                $asignaciones = Asignacion::where('id_curso', $estudiante->id_curso)
                    ->with('materia', 'curso')
                    ->get();
                return response()->json([
                    'calificaciones' => $calificaciones,
                    'asignaciones' => $asignaciones,
                ]);
            }
        } elseif ($user->rol->nombre === 'padre_familia') {
            $padre = \App\Models\Padre::where('id_usuario', $user->id_usuario)->first();
            if ($padre) {
                $hijos = $padre->estudiantes()->pluck('estudiantes.id_estudiante');
                $calificaciones = Calificacion::whereIn('id_estudiante', $hijos)
                    ->with('tipoEvaluacion', 'asignacion.materia', 'periodo', 'estudiante.usuario')
                    ->get();
            }
        } elseif ($user->rol->nombre === 'docente') {
            $docente = Docente::where('id_usuario', $user->id_usuario)->first();
            if ($docente) {
                $ids = $docente->asignaciones()->pluck('id_asignacion');
                $calificaciones = Calificacion::whereIn('id_asignacion', $ids)
                    ->with('tipoEvaluacion', 'asignacion.materia', 'periodo', 'estudiante.usuario')
                    ->get();
                $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
                    ->with('materia', 'curso')
                    ->get();
                return response()->json([
                    'calificaciones' => $calificaciones,
                    'asignaciones' => $asignaciones,
                ]);
            }
        }

        return response()->json($calificaciones);
    }

    public function byStudent(Request $request, $idEstudiante)
    {
        $calificaciones = Calificacion::where('id_estudiante', $idEstudiante)
            ->with('tipoEvaluacion', 'asignacion.materia', 'periodo')
            ->get();

        return response()->json($calificaciones);
    }
}
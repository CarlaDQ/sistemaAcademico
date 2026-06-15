<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Calificacion;
use App\Models\Citacion;
use App\Models\Docente;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index()
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
            ->with('materia', 'curso')
            ->get();

        return view('docente.reportes.index', compact('asignaciones'));
    }

    public function estudiante(Request $request, $idEstudiante)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
            ->with('materia', 'curso')
            ->get();

        $estudiantes = collect();
        $estudiante = null;
        $calificaciones = collect();
        $promedioGeneral = null;
        $citaciones = collect();
        $asignacionId = $request->get('asignacion');

        if ($idEstudiante == 0 && $asignacionId) {
            $asig = $asignaciones->firstWhere('id_asignacion', $asignacionId);
            if ($asig) {
                $estudiantes = Estudiante::where('id_curso', $asig->id_curso)
                    ->where('activo', true)->with('usuario')->get();
            }
        }

        if ($idEstudiante > 0) {
            $estudiante = Estudiante::with('usuario', 'curso')
                ->where('id_estudiante', $idEstudiante)
                ->where('activo', true)
                ->firstOrFail();

            if ($asignacionId) {
                $calificaciones = Calificacion::where('id_estudiante', $idEstudiante)
                    ->where('id_asignacion', $asignacionId)
                    ->with('tipoEvaluacion', 'periodo')
                    ->get();

                $promedioGeneral = $calificaciones->avg('nota');
            }

            $citaciones = Citacion::where('id_estudiante', $idEstudiante)
                ->where('id_docente', $docente->id_docente)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('docente.reportes.estudiante', compact(
            'estudiante', 'calificaciones', 'asignaciones',
            'asignacionId', 'promedioGeneral', 'citaciones',
            'idEstudiante', 'estudiantes'
        ));
    }

    public function curso(Request $request, $idCurso)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
            ->with('materia', 'curso')
            ->get();

        $asignacionId = $request->get('asignacion');
        $asignacion = null;
        $estudiantes = collect();
        $promedioCurso = null;
        $aprobados = 0;
        $reprobados = 0;

        if ($asignacionId) {
            $asignacion = $asignaciones->firstWhere('id_asignacion', $asignacionId);

            if ($asignacion) {
                $estudiantes = Estudiante::where('id_curso', $asignacion->id_curso)
                    ->where('activo', true)
                    ->with('usuario')
                    ->get()
                    ->map(function ($e) use ($asignacionId) {
                        $califs = Calificacion::where('id_estudiante', $e->id_estudiante)
                            ->where('id_asignacion', $asignacionId)
                            ->with('tipoEvaluacion', 'periodo')
                            ->get();

                        $e->calificaciones = $califs;
                        $e->promedio = $califs->avg('nota');
                        $e->actividades_count = $califs->count();
                        return $e;
                    });

                $promedioCurso = $estudiantes->avg('promedio');
                $aprobados = $estudiantes->filter(fn($e) => ($e->promedio ?? 0) >= 70)->count();
                $reprobados = $estudiantes->filter(fn($e) => ($e->promedio ?? 0) < 70)->count();
            }
        }

        return view('docente.reportes.curso', compact(
            'estudiantes', 'asignaciones', 'asignacion',
            'asignacionId', 'idCurso', 'promedioCurso',
            'aprobados', 'reprobados'
        ));
    }
}
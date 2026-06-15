<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\Citacion;
use App\Models\Estudiante;
use Illuminate\Http\Request;


class EstudianteController extends Controller
{
    public function notas()
    {
        $estudiante = Estudiante::where('id_usuario', session('user_id'))->firstOrFail();

        $calificaciones = Calificacion::where('id_estudiante', $estudiante->id_estudiante)
            ->with('asignacion.materia', 'asignacion.curso', 'tipoEvaluacion', 'periodo')
            ->get();

        $agrupadas = $calificaciones->groupBy('id_asignacion');

        $materias = collect();
        foreach ($agrupadas as $asignacionId => $notas) {
            $first = $notas->first();
            $materias->push([
                'asignacion' => $first->asignacion,
                'calificaciones' => $notas,
                'promedio' => $notas->avg('nota'),
            ]);
        }

        $promedioGeneral = $calificaciones->avg('nota');

        return view('estudiante.notas.index', compact('materias', 'promedioGeneral', 'estudiante'));
    }

    public function asistencia(Request $request)
    {
        $estudiante = Estudiante::where('id_usuario', session('user_id'))->firstOrFail();

        $query = Asistencia::where('id_estudiante', $estudiante->id_estudiante)
            ->with('asignacion.materia');

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $asistencias = $query->orderBy('fecha', 'desc')->get();

        $total = $asistencias->count();
        $presentes = $asistencias->where('estado', 'presente')->count();
        $ausentes = $asistencias->where('estado', 'ausente')->count();
        $tardanzas = $asistencias->where('estado', 'tardanza')->count();
        $justificados = $asistencias->where('estado', 'justificado')->count();

        $porcentajePresente = $total > 0 ? round(($presentes / $total) * 100, 1) : 0;
        $porcentajeAusente = $total > 0 ? round(($ausentes / $total) * 100, 1) : 0;
        $porcentajeTardanza = $total > 0 ? round(($tardanzas / $total) * 100, 1) : 0;
        $porcentajeJustificado = $total > 0 ? round(($justificados / $total) * 100, 1) : 0;

        return view('estudiante.asistencia.index', compact(
            'asistencias', 'presentes', 'ausentes', 'tardanzas', 'justificados',
            'porcentajePresente', 'porcentajeAusente', 'porcentajeTardanza', 'porcentajeJustificado',
            'total', 'estudiante'
        ));
    }

    public function citaciones()
    {
        $estudiante = Estudiante::where('id_usuario', session('user_id'))->firstOrFail();

        $citaciones = Citacion::where('id_estudiante', $estudiante->id_estudiante)
            ->with('docente.usuario')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('estudiante.citaciones.index', compact('citaciones', 'estudiante'));
    }
}

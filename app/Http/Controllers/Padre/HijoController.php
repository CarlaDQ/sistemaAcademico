<?php

namespace App\Http\Controllers\Padre;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\Citacion;
use App\Models\Estudiante;
use App\Models\Padre;
use App\Models\Asignacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class HijoController extends Controller
{
    public function index()
    {
        $padre = Padre::where('id_usuario', session('user_id'))->firstOrFail();

        $hijos = $padre->estudiantes()
            ->where('activo', true)
            ->with('usuario', 'curso')
            ->get()
            ->map(function ($hijo) {
                $calificaciones = Calificacion::where('id_estudiante', $hijo->id_estudiante)->get();
                $asistencias = Asistencia::where('id_estudiante', $hijo->id_estudiante)->get();
                $totalAsistencia = $asistencias->count();
                $presentes = $asistencias->where('estado', 'presente')->count();

                $hijo->promedio = $calificaciones->count() > 0 ? round($calificaciones->avg('nota'), 2) : 0;
                $hijo->asistencia_pct = $totalAsistencia > 0 ? round(($presentes / $totalAsistencia) * 100, 1) : 0;

                return $hijo;
            });

        return view('padre.hijos.index', compact('hijos', 'padre'));
    }

    public function show($id)
    {
        $padre = Padre::where('id_usuario', session('user_id'))->firstOrFail();

        $hijo = Estudiante::where('id_estudiante', $id)
            ->where('activo', true)
            ->with('usuario', 'curso')
            ->firstOrFail();

        $esHijo = $padre->estudiantes()->where('estudiante_tutor.id_estudiante', $id)->exists();
        if (!$esHijo) {
            abort(403, 'No tienes permiso para ver este estudiante.');
        }

        $calificaciones = Calificacion::where('id_estudiante', $id)
            ->with('asignacion.materia', 'asignacion.curso', 'tipoEvaluacion', 'periodo')
            ->get();

        $agrupadas = $calificaciones->groupBy('id_asignacion');
        $materias = [];
        foreach ($agrupadas as $asignacionId => $notas) {
            $first = $notas->first();
            $materias[] = [
                'asignacion' => $first->asignacion,
                'calificaciones' => $notas,
                'promedio' => $notas->avg('nota'),
            ];
        }

        $promedioGeneral = $calificaciones->count() > 0 ? round($calificaciones->avg('nota'), 2) : 0;

        $asistencias = Asistencia::where('id_estudiante', $id)
            ->with('asignacion.materia')
            ->orderBy('fecha', 'desc')
            ->get();

        $totalAsis = $asistencias->count();
        $presentes = $asistencias->where('estado', 'presente')->count();
        $ausentes = $asistencias->where('estado', 'ausente')->count();
        $tardanzas = $asistencias->where('estado', 'tardanza')->count();
        $justificados = $asistencias->where('estado', 'justificado')->count();
        $permisos = $asistencias->where('estado', 'permiso')->count();

        $pctPresente = $totalAsis > 0 ? round(($presentes / $totalAsis) * 100, 1) : 0;
        $pctAusente = $totalAsis > 0 ? round(($ausentes / $totalAsis) * 100, 1) : 0;
        $pctTardanza = $totalAsis > 0 ? round(($tardanzas / $totalAsis) * 100, 1) : 0;
        $pctPermiso = $totalAsis > 0 ? round(($permisos / $totalAsis) * 100, 1) : 0;

        $citaciones = Citacion::where('id_estudiante', $id)
            ->with('docente.usuario')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('padre.hijos.show', compact(
            'hijo', 'materias', 'promedioGeneral',
            'asistencias', 'presentes', 'ausentes', 'tardanzas', 'justificados', 'permisos',
            'pctPresente', 'pctAusente', 'pctTardanza', 'pctPermiso', 'totalAsis',
            'citaciones', 'calificaciones'
        ));
    }

    public function permisoCrear($id)
    {
        $padre = Padre::where('id_usuario', session('user_id'))->firstOrFail();

        $hijo = Estudiante::where('id_estudiante', $id)
            ->where('activo', true)
            ->with('usuario', 'curso')
            ->firstOrFail();

        $esHijo = $padre->estudiantes()->where('estudiante_tutor.id_estudiante', $id)->exists();
        if (!$esHijo) {
            abort(403, 'No tienes permiso para este estudiante.');
        }

        $asignaciones = Asignacion::where('id_curso', $hijo->id_curso)
            ->where('activo', true)
            ->with('materia', 'docente.usuario')
            ->get();

        return view('padre.permisos.crear', compact('hijo', 'asignaciones'));
    }

    public function permisoGuardar(Request $request)
    {
        $request->validate([
            'id_estudiante' => 'required|exists:estudiantes,id_estudiante',
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'motivo' => 'required|string|max:500',
            'fecha' => 'required|date',
        ]);

        $padre = Padre::where('id_usuario', session('user_id'))->firstOrFail();
        $hijo = Estudiante::findOrFail($request->id_estudiante);
        $esHijo = $padre->estudiantes()->where('estudiante_tutor.id_estudiante', $request->id_estudiante)->exists();
        if (!$esHijo) {
            abort(403, 'No tienes permiso para este estudiante.');
        }

        DB::table('asistencia')->insert([
            'id_estudiante' => $request->id_estudiante,
            'id_asignacion' => $request->id_asignacion,
            'fecha' => $request->fecha,
            'hora_registro' => now(),
            'estado' => 'permiso',
            'observacion' => 'PERMISO SOLICITADO POR PADRE: ' . $request->motivo,
            'registrado_por' => session('user_id'),
            'created_at' => now(),
        ]);

        return redirect()->route('padre.hijos.show', $request->id_estudiante)
            ->with('success', 'Permiso solicitado correctamente.');
    }
}

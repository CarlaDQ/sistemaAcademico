<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Asistencia;
use App\Models\Docente;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();
        $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
            ->with('materia', 'curso')
            ->get();

        $asignacionId = $request->get('asignacion');
        $fecha = $request->get('fecha', now()->format('Y-m-d'));
        $estudiantes = collect();
        $asistenciasExistentes = collect();

        if ($asignacionId) {
            $asignacion = Asignacion::where('id_asignacion', $asignacionId)
                ->where('id_docente', $docente->id_docente)
                ->firstOrFail();
            $estudiantes = $asignacion->curso->estudiantes()
                ->where('activo', true)
                ->with('usuario')
                ->get();

            $asistenciasExistentes = Asistencia::where('id_asignacion', $asignacionId)
                ->where('fecha', $fecha)
                ->get()
                ->keyBy('id_estudiante');
        }

        return view('docente.asistencia.index', compact(
            'asignaciones', 'asignacionId', 'fecha', 'estudiantes', 'asistenciasExistentes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'fecha' => 'required|date',
            'asistencia' => 'required|array',
            'asistencia.*' => 'required|in:presente,ausente,tardanza,justificado,permiso',
        ]);

        $asignacionId = $request->id_asignacion;
        $fecha = $request->fecha;

        DB::transaction(function () use ($request, $asignacionId, $fecha) {
            foreach ($request->asistencia as $idEstudiante => $estado) {
                $existing = Asistencia::where('id_estudiante', $idEstudiante)
                    ->where('id_asignacion', $asignacionId)
                    ->where('fecha', $fecha)
                    ->first();

                $data = [
                    'estado' => $estado,
                    'registrado_por' => session('user_id'),
                    'hora_registro' => now(),
                ];

                if ($existing && $existing->estado == 'permiso' && $estado != 'permiso') {
                    $data['observacion'] = $existing->observacion;
                }

                Asistencia::updateOrCreate(
                    [
                        'id_estudiante' => $idEstudiante,
                        'id_asignacion' => $asignacionId,
                        'fecha' => $fecha,
                    ],
                    $data
                );
            }
        });

        return redirect()->route('docente.asistencia.index', [
            'asignacion' => $asignacionId,
            'fecha' => $fecha,
        ])->with('success', 'Asistencia guardada correctamente.');
    }
}

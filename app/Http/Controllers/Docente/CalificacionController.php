<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Calificacion;
use App\Models\Docente;
use App\Models\Periodo;
use App\Models\TipoEvaluacion;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class CalificacionController extends Controller
{
    public function index()
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();
        $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
            ->with('materia', 'curso')
            ->get();
        $periodos = Periodo::whereHas('anioLectivo', function ($q) {
            $q->where('activo', true);
        })->get();

        return view('docente.calificaciones.index', compact('asignaciones', 'periodos'));
    }

    public function getActividades($idAsignacion)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();
        $asignacion = Asignacion::where('id_asignacion', $idAsignacion)
            ->where('id_docente', $docente->id_docente)
            ->with('materia', 'curso')
            ->firstOrFail();

        if (!$asignacion->curso) {
            return response()->json([
                'estudiantes' => [],
                'actividades' => [],
                'calificaciones' => [],
                'message' => 'El curso no existe para esta asignación.',
            ]);
        }

        $estudiantes = $asignacion->curso->estudiantes()
            ->where('activo', true)
            ->with('usuario')
            ->orderBy('id_estudiante')
            ->get();

        $actividades = TipoEvaluacion::whereHas('calificaciones', function ($q) use ($idAsignacion) {
            $q->where('id_asignacion', $idAsignacion);
        })->get();

        $calificaciones = Calificacion::where('id_asignacion', $idAsignacion)
            ->with('tipoEvaluacion')
            ->get()
            ->groupBy('id_estudiante');

        return response()->json([
            'estudiantes' => $estudiantes->map(function ($e) {
                $nombre = optional($e->usuario)->nombre ?? 'Desconocido';
                $apellido = optional($e->usuario)->apellido ?? '';
                $ci = optional($e->usuario)->ci ?? '';
                return [
                    'id' => $e->id_estudiante,
                    'nombre' => $nombre . ' ' . $apellido,
                    'ci' => $ci,
                ];
            }),
            'actividades' => $actividades->values(),
            'calificaciones' => $calificaciones,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'id_periodo' => 'required|exists:periodos_evaluacion,id_periodo',
            'actividades' => 'required|array',
            'actividades.*.nombre' => 'required|string|max:255',
            'notas' => 'required|array',
            'notas.*.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();
        $asignacion = Asignacion::where('id_asignacion', $request->id_asignacion)
            ->where('id_docente', $docente->id_docente)
            ->firstOrFail();
        $notaMaxima = 100;

        DB::transaction(function () use ($request, $asignacion, $notaMaxima) {
            $tipoIds = [];

            foreach ($request->actividades as $index => $actividad) {
                $tipo = TipoEvaluacion::firstOrCreate(
                    ['nombre' => $actividad['nombre']],
                    ['ponderacion' => 1, 'activo' => true]
                );
                $tipoIds[$index] = $tipo->id_tipo;
            }

            foreach ($request->notas as $idEstudiante => $notasPorActividad) {
                foreach ($notasPorActividad as $actIndex => $nota) {
                    if ($nota === null || $nota === '') {
                        continue;
                    }

                    $tipoId = $tipoIds[$actIndex] ?? null;
                    if (!$tipoId) {
                        continue;
                    }

                    Calificacion::updateOrCreate(
                        [
                            'id_estudiante' => $idEstudiante,
                            'id_asignacion' => $asignacion->id_asignacion,
                            'id_periodo' => $request->id_periodo,
                            'id_tipo_eval' => $tipoId,
                        ],
                        [
                            'nota' => (float) $nota,
                            'nota_maxima' => $notaMaxima,
                            'fecha_evaluacion' => now(),
                            'ingresado_por' => session('user_id'),
                        ]
                    );
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Calificaciones guardadas correctamente.']);
    }
}

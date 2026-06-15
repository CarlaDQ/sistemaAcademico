<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Citacion;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\Notificacion;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class CitacionController extends Controller
{
    public function index(Request $request)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $citaciones = Citacion::where('id_docente', $docente->id_docente)
            ->with('estudiante.usuario')
            ->orderBy('created_at', 'desc');

        if ($request->filled('id_curso')) {
            $citaciones->whereHas('estudiante', function ($q) use ($request) {
                $q->where('id_curso', $request->id_curso);
            });
        }

        if ($request->filled('tipo')) {
            $citaciones->where('tipo', $request->tipo);
        }

        $citaciones = $citaciones->get();

        $asignaciones = $docente->asignaciones()->with('curso', 'materia')->get();
        $cursoIds = $asignaciones->pluck('id_curso')->unique();
        $cursos = Curso::whereIn('id_curso', $cursoIds)->where('activo', true)->get();
        $estudiantes = Estudiante::whereIn('id_curso', $cursoIds)
            ->where('activo', true)
            ->with('usuario', 'curso')
            ->get()
            ->groupBy(function ($e) {
                return $e->curso->nombre ?? 'Sin curso';
            });

        return view('docente.citaciones.index', compact('citaciones', 'estudiantes', 'docente', 'cursos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_curso' => 'required|exists:cursos,id_curso',
            'id_estudiante' => 'nullable|exists:estudiantes,id_estudiante',
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'tipo' => 'required|in:citacion,aviso,comunicado',
            'fecha_citacion' => 'nullable|date',
            'hora_citacion' => 'nullable',
            'lugar' => 'nullable|string|max:255',
        ]);

        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        if ($request->filled('id_estudiante')) {
            $estudiantes = Estudiante::where('id_estudiante', $request->id_estudiante)
                ->where('id_curso', $request->id_curso)
                ->get();
        } else {
            $estudiantes = Estudiante::where('id_curso', $request->id_curso)
                ->where('activo', true)
                ->get();
        }

        if ($estudiantes->isEmpty()) {
            return redirect()->route('docente.citaciones.index')->with('error', 'No hay estudiantes en el curso seleccionado.');
        }

        DB::transaction(function () use ($request, $docente, $estudiantes) {
            foreach ($estudiantes as $estudiante) {
                $citacion = Citacion::create([
                    'id_docente' => $docente->id_docente,
                    'id_estudiante' => $estudiante->id_estudiante,
                    'titulo' => $request->titulo,
                    'mensaje' => $request->mensaje,
                    'tipo' => $request->tipo,
                    'fecha_citacion' => $request->fecha_citacion,
                    'hora_citacion' => $request->hora_citacion,
                    'lugar' => $request->lugar,
                    'estado' => 'pendiente',
                ]);

                $estudiante->load('tutores.padre.usuario');
                foreach ($estudiante->tutores as $tutor) {
                    if ($tutor->padre && $tutor->padre->usuario) {
                        Notificacion::create([
                            'id_usuario_destino' => $tutor->padre->usuario->id_usuario,
                            'id_usuario_origen' => session('user_id'),
                            'titulo' => 'Nueva ' . $request->tipo . ': ' . $request->titulo,
                            'mensaje' => $request->mensaje,
                            'tipo' => 'general',
                            'canal' => 'app',
                            'leido' => false,
                            'entidad_tipo' => 'citacion',
                            'entidad_id' => $citacion->id_citacion,
                        ]);
                    }
                }
            }
        });

        $count = $estudiantes->count();
        $msg = $count > 1
            ? ucfirst($request->tipo) . " enviada a {$count} estudiantes del curso."
            : ucfirst($request->tipo) . ' creada y notificación enviada a los padres.';

        return redirect()->route('docente.citaciones.index')->with('success', $msg);
    }

    public function getEstudiantes($idCurso)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();
        $cursoIds = $docente->asignaciones()->pluck('id_curso')->unique();

        if (!in_array($idCurso, $cursoIds->toArray())) {
            return response()->json(['error' => 'Curso no autorizado'], 403);
        }

        $estudiantes = Estudiante::where('id_curso', $idCurso)
            ->where('activo', true)
            ->with('usuario')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id_estudiante,
                    'nombre' => optional($e->usuario)->nombre . ' ' . optional($e->usuario)->apellido,
                ];
            });

        return response()->json($estudiantes);
    }
}

<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\Asignacion;
use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\Citacion;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
            ->with('materia', 'curso')
            ->get();

        $totalMaterias = $asignaciones->count();
        $totalCursos = $asignaciones->pluck('curso.nombre')->unique()->count();
        $totalEstudiantes = 0;
        $cursosData = [];

        foreach ($asignaciones->groupBy('id_curso') as $cursoId => $cursoAsigs) {
            $curso = $cursoAsigs->first()->curso;
            $count = $curso->estudiantes()->where('activo', true)->count();
            $totalEstudiantes += $count;
            $materias = $cursoAsigs->map(function ($a) {
                return $a->materia->nombre;
            })->implode(', ');
            $cursosData[] = [
                'nombre' => $curso->nombre,
                'estudiantes' => $count,
                'materias' => $materias,
            ];
        }

        $hoy = Carbon::today();
        $asistenciaHoy = Asistencia::whereHas('estudiante', function ($q) use ($docente) {
            $q->whereIn('id_curso', function ($sub) use ($docente) {
                $sub->select('id_curso')->from('asignaciones')->where('id_docente', $docente->id_docente);
            });
        })->whereDate('fecha', $hoy)->get();

        $presentes = $asistenciaHoy->where('estado', 'presente')->count();
        $ausentes = $asistenciaHoy->where('estado', 'ausente')->count();
        $tardanzas = $asistenciaHoy->where('estado', 'tardanza')->count();

        $citacionesPendientes = Citacion::where('id_docente', $docente->id_docente)
            ->where('estado', 'pendiente')
            ->count();

        $recientes = Citacion::where('id_docente', $docente->id_docente)
            ->with('estudiante.usuario')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('docente.dashboard', compact(
            'docente', 'asignaciones', 'totalMaterias', 'totalCursos', 'totalEstudiantes',
            'cursosData', 'presentes', 'ausentes', 'tardanzas',
            'citacionesPendientes', 'recientes'
        ));
    }
}

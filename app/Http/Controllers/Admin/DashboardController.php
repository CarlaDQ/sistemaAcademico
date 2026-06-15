<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Docente;
use App\Models\Curso;
use App\Models\Asignacion;
use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\Usuario;
use App\Models\Horario;
use App\Models\Padre;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEstudiantes = Estudiante::where('activo', true)->count();
        $totalDocentes = Docente::where('activo', true)->count();
        $totalCursos = Curso::where('activo', true)->count();
        $totalPadres = Padre::count();

        $hoy = Carbon::today();
        $asistenciaHoy = Asistencia::whereDate('fecha', $hoy)->get();
        $presentesHoy = $asistenciaHoy->where('estado', 'presente')->count();
        $ausentesHoy = $asistenciaHoy->where('estado', 'ausente')->count();
        $tardanzasHoy = $asistenciaHoy->where('estado', 'tardanza')->count();
        $totalAsistenciaHoy = $asistenciaHoy->count();

        $promedioGeneral = Calificacion::avg('nota');

        $estudiantesPorCurso = Curso::where('activo', true)
            ->withCount(['estudiantes' => function ($q) {
                $q->where('activo', true);
            }])
            ->get()
            ->map(function ($c) {
                return [
                    'label' => $c->nombre,
                    'count' => $c->estudiantes_count,
                ];
            });

        $recientes = Usuario::with('rol')
            ->where('estado', 'activo')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($u) {
                $email = '****@sistemaacademico.com';
                return (object)[
                    'nombre' => $u->nombre . ' ' . $u->apellido,
                    'email' => $email,
                    'rol' => $u->rol->nombre ?? '—',
                    'creado' => $u->created_at,
                ];
            });

        $horarios = Horario::with('asignacion.docente.usuario', 'asignacion.materia', 'asignacion.curso')
            ->where('activo', true)
            ->get()
            ->groupBy('dia_semana');
        $diasOrden = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $horarioGrid = [];
        foreach ($diasOrden as $d) {
            $horarioGrid[$d] = $horarios->get($d, collect())->sortBy('hora_inicio');
        }
        $franjas = ['14:00', '14:40', '15:20', '16:30', '17:10'];

        return view('admin.dashboard', compact(
            'totalEstudiantes', 'totalDocentes', 'totalCursos', 'totalPadres',
            'presentesHoy', 'ausentesHoy', 'tardanzasHoy', 'totalAsistenciaHoy',
            'promedioGeneral', 'estudiantesPorCurso', 'recientes',
            'horarioGrid', 'diasOrden', 'franjas'
        ));
    }
}

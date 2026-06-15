<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Asignacion;
use App\Models\Docente;
use App\Models\Curso;
use App\Models\Materia;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Horario::with('asignacion.docente.usuario', 'asignacion.materia', 'asignacion.curso');

        if ($request->filled('id_docente')) {
            $query->whereHas('asignacion', function ($q) use ($request) {
                $q->where('id_docente', $request->id_docente);
            });
        }

        if ($request->filled('id_curso')) {
            $query->whereHas('asignacion', function ($q) use ($request) {
                $q->where('id_curso', $request->id_curso);
            });
        }

        $horarios = $query->orderBy('dia_semana')->orderBy('hora_inicio')->paginate(50);
        $docentes = Docente::where('activo', true)->with('usuario')->get();
        $cursos = Curso::where('activo', true)->get();

        return view('admin.horarios.index', compact('horarios', 'docentes', 'cursos'));
    }

    public function create()
    {
        $asignaciones = Asignacion::where('activo', true)
            ->with('docente.usuario', 'materia', 'curso')
            ->get();
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        return view('admin.horarios.form', compact('asignaciones', 'dias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'dia_semana' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        Horario::create([
            'id_asignacion' => $validated['id_asignacion'],
            'dia_semana' => $validated['dia_semana'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'activo' => true,
        ]);

        return redirect()->route('admin.horarios.index')->with('success', 'Horario creado correctamente.');
    }

    public function show($id)
    {
        return redirect()->route('admin.horarios.edit', $id);
    }

    public function edit($id)
    {
        $horario = Horario::with('asignacion.materia', 'asignacion.curso')->findOrFail($id);
        $asignaciones = Asignacion::where('activo', true)
            ->with('docente.usuario', 'materia', 'curso')
            ->get();
        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        return view('admin.horarios.form', compact('horario', 'asignaciones', 'dias'));
    }

    public function update(Request $request, $id)
    {
        $horario = Horario::findOrFail($id);

        $validated = $request->validate([
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'dia_semana' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $horario->update($validated);

        return redirect()->route('admin.horarios.index')->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $horario = Horario::findOrFail($id);
        $horario->delete();

        return redirect()->route('admin.horarios.index')->with('success', 'Horario eliminado correctamente.');
    }
}

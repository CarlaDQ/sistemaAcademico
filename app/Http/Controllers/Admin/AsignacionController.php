<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\Curso;
use App\Models\AnioLectivo;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Asignacion::with(['docente.usuario', 'materia', 'curso', 'anioLectivo'])
            ->where('activo', true);

        if ($request->filled('id_docente')) {
            $query->where('id_docente', $request->id_docente);
        }

        if ($request->filled('id_curso')) {
            $query->where('id_curso', $request->id_curso);
        }

        if ($request->filled('id_anio')) {
            $query->where('id_anio', $request->id_anio);
        }

        $asignaciones = $query->orderBy('id_asignacion', 'desc')->paginate(15);
        $docentes = Docente::with('usuario')->where('activo', true)->get();
        $cursos = Curso::where('activo', true)->get();
        $anios = AnioLectivo::where('activo', true)->get();

        return view('admin.asignaciones.index', compact('asignaciones', 'docentes', 'cursos', 'anios'));
    }

    public function create()
    {
        $docentes = Docente::with('usuario')->where('activo', true)->get();
        $materias = Materia::where('activo', true)->get();
        $cursos = Curso::where('activo', true)->get();
        $anios = AnioLectivo::where('activo', true)->get();
        return view('admin.asignaciones.form', compact('docentes', 'materias', 'cursos', 'anios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_docente' => 'required|exists:docentes,id_docente',
            'id_materia' => 'required|exists:materias,id_materia',
            'id_curso' => 'required|exists:cursos,id_curso',
            'id_anio' => 'required|exists:anios_lectivos,id_anio',
        ]);

        $existe = Asignacion::where('id_docente', $validated['id_docente'])
            ->where('id_materia', $validated['id_materia'])
            ->where('id_curso', $validated['id_curso'])
            ->where('id_anio', $validated['id_anio'])
            ->where('activo', true)
            ->first();

        if ($existe) {
            return back()->withInput()->with('error', 'Ya existe una asignación activa con los mismos datos.');
        }

        $validated['activo'] = true;

        $materia = Materia::find($validated['id_materia']);
        $curso = Curso::find($validated['id_curso']);
        $prefijo = strtoupper(substr($materia->nombre ?? '', 0, 3));
        $validated['codigo'] = $prefijo . '-' . ($curso->grado ?? '0') . '00';

        Asignacion::create($validated);

        return redirect()->route('admin.asignaciones.index')->with('success', 'Asignación creada correctamente. Código: ' . $validated['codigo']);
    }

    public function edit($id)
    {
        $asignacion = Asignacion::findOrFail($id);
        $docentes = Docente::with('usuario')->where('activo', true)->get();
        $materias = Materia::where('activo', true)->get();
        $cursos = Curso::where('activo', true)->get();
        $anios = AnioLectivo::where('activo', true)->get();
        return view('admin.asignaciones.form', compact('asignacion', 'docentes', 'materias', 'cursos', 'anios'));
    }

    public function update(Request $request, $id)
    {
        $asignacion = Asignacion::findOrFail($id);

        $validated = $request->validate([
            'id_docente' => 'required|exists:docentes,id_docente',
            'id_materia' => 'required|exists:materias,id_materia',
            'id_curso' => 'required|exists:cursos,id_curso',
            'id_anio' => 'required|exists:anios_lectivos,id_anio',
        ]);

        $existe = Asignacion::where('id_docente', $validated['id_docente'])
            ->where('id_materia', $validated['id_materia'])
            ->where('id_curso', $validated['id_curso'])
            ->where('id_anio', $validated['id_anio'])
            ->where('activo', true)
            ->where('id_asignacion', '!=', $id)
            ->first();

        if ($existe) {
            return back()->withInput()->with('error', 'Ya existe una asignación activa con los mismos datos.');
        }

        $materia = Materia::find($validated['id_materia']);
        $curso = Curso::find($validated['id_curso']);
        $prefijo = strtoupper(substr($materia->nombre ?? '', 0, 3));
        $validated['codigo'] = $prefijo . '-' . ($curso->grado ?? '0') . '00';

        $asignacion->update($validated);

        return redirect()->route('admin.asignaciones.index')->with('success', 'Asignación actualizada correctamente.');
    }

    public function destroy($id)
    {
        $asignacion = Asignacion::findOrFail($id);
        $asignacion->update(['activo' => false]);
        return redirect()->route('admin.asignaciones.index')->with('success', 'Asignación desactivada correctamente.');
    }
}

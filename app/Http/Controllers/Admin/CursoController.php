<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\NivelEducativo;
use App\Models\AnioLectivo;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $query = Curso::with(['nivel', 'anioLectivo', 'estudiantes'])
            ->where('activo', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('grado', 'like', "%{$s}%")
                  ->orWhere('seccion', 'like', "%{$s}%");
            });
        }

        $cursos = $query->orderBy('nombre')->paginate(15);
        $cursos->each(function ($c) {
            $c->estudiantes_count = $c->estudiantes->where('activo', true)->count();
        });

        return view('admin.cursos.index', compact('cursos'));
    }

    public function create()
    {
        $niveles = NivelEducativo::all();
        $anios = AnioLectivo::where('activo', true)->get();
        return view('admin.cursos.form', compact('niveles', 'anios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_nivel' => 'required|exists:niveles_educativos,id_nivel',
            'id_anio' => 'required|exists:anios_lectivos,id_anio',
            'nombre' => 'required|string|max:100',
            'grado' => 'nullable|string|max:50',
            'seccion' => 'nullable|string|max:10',
            'capacidad' => 'nullable|integer|min:1|max:100',
        ]);

        $validated['activo'] = true;

        Curso::create($validated);

        return redirect()->route('admin.cursos.index')->with('success', 'Curso creado correctamente.');
    }

    public function edit($id)
    {
        $curso = Curso::findOrFail($id);
        $niveles = NivelEducativo::all();
        $anios = AnioLectivo::where('activo', true)->get();
        return view('admin.cursos.form', compact('curso', 'niveles', 'anios'));
    }

    public function update(Request $request, $id)
    {
        $curso = Curso::findOrFail($id);

        $validated = $request->validate([
            'id_nivel' => 'required|exists:niveles_educativos,id_nivel',
            'id_anio' => 'required|exists:anios_lectivos,id_anio',
            'nombre' => 'required|string|max:100',
            'grado' => 'nullable|string|max:50',
            'seccion' => 'nullable|string|max:10',
            'capacidad' => 'nullable|integer|min:1|max:100',
        ]);

        $curso->update($validated);

        return redirect()->route('admin.cursos.index')->with('success', 'Curso actualizado correctamente.');
    }

    public function destroy($id)
    {
        $curso = Curso::findOrFail($id);
        $curso->update(['activo' => false]);
        return redirect()->route('admin.cursos.index')->with('success', 'Curso desactivado correctamente.');
    }
}

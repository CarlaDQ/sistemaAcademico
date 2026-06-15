<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\NivelEducativo;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Materia::with('nivel')->where('activo', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', "%{$s}%")
                  ->orWhere('codigo', 'like', "%{$s}%");
            });
        }

        if ($request->filled('id_nivel')) {
            $query->where('id_nivel', $request->id_nivel);
        }

        $materias = $query->orderBy('nombre')->paginate(15);
        $niveles = NivelEducativo::all();

        return view('admin.materias.index', compact('materias', 'niveles'));
    }

    public function create()
    {
        $niveles = NivelEducativo::all();
        return view('admin.materias.form', compact('niveles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_nivel' => 'required|exists:niveles_educativos,id_nivel',
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20|unique:materias,codigo',
            'horas_semanales' => 'nullable|integer|min:1|max:40',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $validated['activo'] = true;

        Materia::create($validated);

        return redirect()->route('admin.materias.index')->with('success', 'Materia creada correctamente.');
    }

    public function edit($id)
    {
        $materia = Materia::findOrFail($id);
        $niveles = NivelEducativo::all();
        return view('admin.materias.form', compact('materia', 'niveles'));
    }

    public function update(Request $request, $id)
    {
        $materia = Materia::findOrFail($id);

        $validated = $request->validate([
            'id_nivel' => 'required|exists:niveles_educativos,id_nivel',
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:20|unique:materias,codigo,' . $id . ',id_materia',
            'horas_semanales' => 'nullable|integer|min:1|max:40',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $materia->update($validated);

        return redirect()->route('admin.materias.index')->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy($id)
    {
        $materia = Materia::findOrFail($id);
        $materia->update(['activo' => false]);
        return redirect()->route('admin.materias.index')->with('success', 'Materia desactivada correctamente.');
    }
}

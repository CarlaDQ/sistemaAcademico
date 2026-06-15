<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use App\Models\Geocerca;
use App\Models\Docente;
use App\Models\Asignacion;
use Illuminate\Http\Request;

class GeocercaController extends Controller
{
    public function index()
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
            ->with('materia', 'curso', 'geocercas')
            ->get();

        $geocercas = Geocerca::whereHas('asignacion', function ($q) use ($docente) {
            $q->where('id_docente', $docente->id_docente);
        })->with('asignacion.materia', 'asignacion.curso')->get();

        return view('docente.geocercas.index', compact('asignaciones', 'geocercas'));
    }

    public function store(Request $request)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $request->validate([
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:300',
            'latitud_centro' => 'required|numeric|between:-90,90',
            'longitud_centro' => 'required|numeric|between:-180,180',
            'radio_metros' => 'required|numeric|min:10|max:1000',
            'horario_inicio' => 'nullable',
            'horario_fin' => 'nullable',
            'dias_semana' => 'nullable|array',
            'dias_semana.*' => 'in:lunes,martes,miercoles,jueves,viernes,sabado',
        ]);

        Asignacion::where('id_asignacion', $request->id_asignacion)
            ->where('id_docente', $docente->id_docente)
            ->firstOrFail();

        $dias = $request->dias_semana
            ? implode(',', $request->dias_semana)
            : 'lunes,martes,miercoles,jueves,viernes';

        Geocerca::create([
            'id_asignacion' => $request->id_asignacion,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'latitud_centro' => $request->latitud_centro,
            'longitud_centro' => $request->longitud_centro,
            'radio_metros' => $request->radio_metros,
            'horario_inicio' => $request->horario_inicio,
            'horario_fin' => $request->horario_fin,
            'dias_semana' => $dias,
            'activo' => true,
        ]);

        return redirect()->route('docente.geocercas.index')
            ->with('success', 'Geocerca creada correctamente.');
    }

    public function edit($id)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $geocerca = Geocerca::where('id_geocerca', $id)
            ->whereHas('asignacion', fn($q) => $q->where('id_docente', $docente->id_docente))
            ->with('asignacion.materia', 'asignacion.curso')
            ->firstOrFail();

        $asignaciones = Asignacion::where('id_docente', $docente->id_docente)
            ->with('materia', 'curso', 'geocercas')
            ->get();

        return response()->json([
            'geocerca' => $geocerca,
            'asignaciones' => $asignaciones,
            'dias_seleccionados' => explode(',', $geocerca->dias_semana),
        ]);
    }

    public function update(Request $request, $id)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $request->validate([
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:300',
            'latitud_centro' => 'required|numeric|between:-90,90',
            'longitud_centro' => 'required|numeric|between:-180,180',
            'radio_metros' => 'required|numeric|min:10|max:1000',
            'horario_inicio' => 'nullable',
            'horario_fin' => 'nullable',
            'dias_semana' => 'nullable|array',
            'dias_semana.*' => 'in:lunes,martes,miercoles,jueves,viernes,sabado',
        ]);

        $geocerca = Geocerca::where('id_geocerca', $id)
            ->whereHas('asignacion', fn($q) => $q->where('id_docente', $docente->id_docente))
            ->firstOrFail();

        $dias = $request->dias_semana
            ? implode(',', $request->dias_semana)
            : 'lunes,martes,miercoles,jueves,viernes';

        $geocerca->update([
            'id_asignacion' => $request->id_asignacion,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'latitud_centro' => $request->latitud_centro,
            'longitud_centro' => $request->longitud_centro,
            'radio_metros' => $request->radio_metros,
            'horario_inicio' => $request->horario_inicio,
            'horario_fin' => $request->horario_fin,
            'dias_semana' => $dias,
        ]);

        return redirect()->route('docente.geocercas.index')
            ->with('success', 'Geocerca actualizada correctamente.');
    }

    public function destroy($id)
    {
        $docente = Docente::where('id_usuario', session('user_id'))->firstOrFail();

        $geocerca = Geocerca::where('id_geocerca', $id)
            ->whereHas('asignacion', fn($q) => $q->where('id_docente', $docente->id_docente))
            ->firstOrFail();

        $geocerca->delete();

        return redirect()->route('docente.geocercas.index')
            ->with('success', 'Geocerca eliminada correctamente.');
    }
}

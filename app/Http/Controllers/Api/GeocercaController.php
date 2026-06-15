<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Geocerca;
use App\Models\Docente;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class GeocercaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('rol');

        if ($user->rol->nombre === 'estudiante') {
            $estudiante = Estudiante::where('id_usuario', $user->id_usuario)->first();
            if (!$estudiante) {
                return response()->json([], 200);
            }
            $geocercas = Geocerca::whereHas('asignacion', function ($q) use ($estudiante) {
                $q->where('id_curso', $estudiante->id_curso);
            })->with('asignacion.materia', 'asignacion.curso')->get();
            return response()->json($geocercas);
        }

        $docente = Docente::where('id_usuario', $user->id_usuario)->first();
        if (!$docente) {
            return response()->json([], 200);
        }

        $geocercas = Geocerca::whereHas('asignacion', function ($q) use ($docente) {
            $q->where('id_docente', $docente->id_docente);
        })->with('asignacion.materia', 'asignacion.curso')->get();

        return response()->json($geocercas);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $docente = Docente::where('id_usuario', $user->id_usuario)->firstOrFail();

        $request->validate([
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:300',
            'latitud_centro' => 'required|numeric',
            'longitud_centro' => 'required|numeric',
            'radio_metros' => 'required|numeric|min:10|max:1000',
            'horario_inicio' => 'nullable',
            'horario_fin' => 'nullable',
            'dias_semana' => 'nullable|string',
        ]);

        $asignacion = \App\Models\Asignacion::where('id_asignacion', $request->id_asignacion)
            ->where('id_docente', $docente->id_docente)->firstOrFail();

        $geocerca = Geocerca::create([
            'id_asignacion' => $asignacion->id_asignacion,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'latitud_centro' => $request->latitud_centro,
            'longitud_centro' => $request->longitud_centro,
            'radio_metros' => $request->radio_metros,
            'horario_inicio' => $request->horario_inicio,
            'horario_fin' => $request->horario_fin,
            'dias_semana' => $request->dias_semana ?? 'lunes,martes,miercoles,jueves,viernes',
            'activo' => true,
        ]);

        return response()->json($geocerca, 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $docente = Docente::where('id_usuario', $user->id_usuario)->firstOrFail();

        $geocerca = Geocerca::where('id_geocerca', $id)
            ->whereHas('asignacion', fn($q) => $q->where('id_docente', $docente->id_docente))
            ->firstOrFail();

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string|max:300',
            'latitud_centro' => 'sometimes|numeric',
            'longitud_centro' => 'sometimes|numeric',
            'radio_metros' => 'sometimes|numeric|min:10|max:1000',
            'horario_inicio' => 'nullable',
            'horario_fin' => 'nullable',
            'dias_semana' => 'nullable|string',
            'activo' => 'sometimes|boolean',
        ]);

        $geocerca->update($validated);
        return response()->json($geocerca);
    }

    public function destroy($id)
    {
        $user = request()->user();
        $docente = Docente::where('id_usuario', $user->id_usuario)->firstOrFail();

        $geocerca = Geocerca::where('id_geocerca', $id)
            ->whereHas('asignacion', fn($q) => $q->where('id_docente', $docente->id_docente))
            ->firstOrFail();

        $geocerca->delete();
        return response()->json(['message' => 'Geocerca eliminada.']);
    }
}
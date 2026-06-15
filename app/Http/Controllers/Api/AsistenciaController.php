<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asignacion;
use App\Models\Asistencia;
use App\Models\Geocerca;
use App\Models\Docente;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function register(Request $request)
    {
        $user = $request->user();
        $estudiante = Estudiante::where('id_usuario', $user->id_usuario)->first();

        if (!$estudiante) {
            return response()->json(['message' => 'Solo estudiantes pueden registrar asistencia.'], 403);
        }

        $request->validate([
            'id_asignacion' => 'required|exists:asignaciones,id_asignacion',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'fecha' => 'required|date',
        ]);

        $asignacion = Asignacion::with('curso')->findOrFail($request->id_asignacion);

        $geocercas = Geocerca::where('id_asignacion', $asignacion->id_asignacion)
            ->where('activo', true)->get();

        if ($geocercas->isEmpty()) {
            return response()->json(['message' => 'No hay geocercas configuradas para esta asignación.'], 400);
        }

        $horaActual = now()->format('H:i:s');

        $dentroDeGeocerca = false;
        $geocercaUsada = null;
        $distanciaMinima = null;

        foreach ($geocercas as $geo) {
            $distancia = $this->calcularDistancia(
                $request->latitud, $request->longitud,
                $geo->latitud_centro, $geo->longitud_centro
            );
            if ($distancia <= $geo->radio_metros) {
                $dentroDeGeocerca = true;
                $geocercaUsada = $geo;
                $distanciaMinima = $distancia;
                break;
            }
            if ($distanciaMinima === null || $distancia < $distanciaMinima) {
                $distanciaMinima = $distancia;
            }
        }

        $estado = $dentroDeGeocerca ? 'presente' : 'ausente';

        $asistencia = Asistencia::updateOrCreate(
            [
                'id_estudiante' => $estudiante->id_estudiante,
                'id_asignacion' => $asignacion->id_asignacion,
                'fecha' => $request->fecha,
            ],
            [
                'estado' => $estado,
                'id_geocerca' => $geocercaUsada?->id_geocerca,
                'latitud_registro' => $request->latitud,
                'longitud_registro' => $request->longitud,
                'distancia_metros' => round($distanciaMinima, 2),
                'dentro_geocerca' => $dentroDeGeocerca,
                'hora_registro' => now()->format('H:i:s'),
                'dispositivo_origen' => 'movil',
                'registrado_por' => $user->id_usuario,
            ]
        );

        return response()->json([
            'asistencia' => $asistencia,
            'geocerca' => $geocercaUsada,
            'horario_inicio' => $geocercaUsada?->horario_inicio,
            'horario_fin' => $geocercaUsada?->horario_fin,
            'dias_semana' => $geocercaUsada?->dias_semana,
            'dentro_horario' => true,
            'hora_actual' => substr($horaActual, 0, 5),
            'mensaje' => $estado === 'presente'
                ? 'Asistencia registrada correctamente.'
                : 'No se encuentra dentro del área de clase. Asistencia marcada como ausente.',
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user()->load('rol');
        $asistencias = collect();

        if ($user->rol->nombre === 'estudiante') {
            $estudiante = Estudiante::where('id_usuario', $user->id_usuario)->first();
            if ($estudiante) {
                $asistencias = Asistencia::where('id_estudiante', $estudiante->id_estudiante)
                    ->with('asignacion.materia')
                    ->orderBy('fecha', 'desc')
                    ->get();
            }
        } elseif ($user->rol->nombre === 'docente') {
            $docente = Docente::where('id_usuario', $user->id_usuario)->first();
            if ($docente) {
                $ids = $docente->asignaciones()->pluck('id_asignacion');
                $asistencias = Asistencia::whereIn('id_asignacion', $ids)
                    ->with('estudiante.usuario', 'asignacion.materia')
                    ->orderBy('fecha', 'desc')
                    ->get();
            }
        } elseif ($user->rol->nombre === 'padre_familia') {
            $padre = \App\Models\Padre::where('id_usuario', $user->id_usuario)->first();
            if ($padre) {
                $hijos = $padre->estudiantes()->pluck('estudiantes.id_estudiante');
                $asistencias = Asistencia::whereIn('id_estudiante', $hijos)
                    ->with('estudiante.usuario', 'asignacion.materia')
                    ->orderBy('fecha', 'desc')
                    ->get();
            }
        }

        return response()->json($asistencias);
    }

    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        $radioTierra = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $radioTierra * $c;
    }
}
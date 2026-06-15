@extends('layouts.app')

@section('title', 'Reporte de Estudiante - Docente')

@push('styles')
<style>
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .card-custom {
        background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: none; margin-bottom: 24px;
    }
    .card-custom .card-header {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f); color: #fff;
        border-radius: 14px 14px 0 0; padding: 16px 24px; font-weight: 600; border: none;
    }
    .grade-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
    .grade-high { background: #e8f5e9; color: #2e7d32; }
    .grade-mid { background: #fff3e0; color: #e65100; }
    .grade-low { background: #ffebee; color: #c62828; }
    .table th {
        background: #f4f6f9; border-bottom: 2px solid #1e3c72;
        color: #1e3c72; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.3px;
    }
    .table td { vertical-align: middle; font-size: 0.85rem; }
    .table tbody tr:hover { background: #f8faff; }
    .table tbody tr:nth-child(even) { background: #f8faff; }
    .stat-card {
        background: #fff; border-radius: 12px; padding: 22px;
        border-left: 5px solid #1e3c72; height: 100%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .stat-card .number { font-size: 2rem; font-weight: 700; }
    .stat-card .label { font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .student-header { background: linear-gradient(135deg, #1e3c72, #2a4a7f); border-radius: 14px; padding: 24px; color: #fff; }
    .student-header h5 { font-weight: 700; margin-bottom: 4px; }
    .student-header p { opacity: 0.85; font-size: 0.85rem; margin-bottom: 0; }
    .form-select:focus { border-color: #1e3c72; box-shadow: 0 0 0 2px rgba(30,60,114,0.15); }
    .btn-generate {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f); color: #fff;
        border: none; border-radius: 8px; font-weight: 600; transition: all 0.2s;
    }
    .btn-generate:hover { background: linear-gradient(135deg, #152a4f, #1e3c72); color: #fff; transform: translateY(-1px); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1e3c72;"><i class="bi bi-person-circle me-2"></i>Reporte por Estudiante</h3>
            <p class="text-muted small mb-0">Calificaciones detalladas y citaciones del estudiante</p>
        </div>
        <a href="{{ route('docente.reportes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-header"><i class="bi bi-funnel me-2"></i>Parámetros de Búsqueda</div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold"><i class="bi bi-book me-1"></i> Asignación</label>
                    <select id="selAsignacion" class="form-select" onchange="navigateAsignacion(this.value)">
                        <option value="">Seleccionar asignación...</option>
                        @foreach($asignaciones as $a)
                            <option value="{{ $a->id_asignacion }}" {{ $a->id_asignacion == $asignacionId ? 'selected' : '' }}>
                                [{{ $a->codigo ?? '' }}] {{ optional($a->materia)->nombre ?? 'N/A' }} - {{ optional($a->curso)->nombre ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i> Estudiante</label>
                    <select id="selEstudiante" class="form-select" {{ $estudiantes->isEmpty() ? 'disabled' : '' }} onchange="navigateEstudiante(this.value)">
                        <option value="">Seleccionar estudiante...</option>
                        @foreach($estudiantes as $e)
                            <option value="{{ $e->id_estudiante }}" {{ $e->id_estudiante == $idEstudiante ? 'selected' : '' }}>
                                {{ optional($e->usuario)->nombre ?? 'Desconocido' }} {{ optional($e->usuario)->apellido ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-generate w-100 py-2" onclick="generateReport()">
                        <i class="bi bi-search me-1"></i> Generar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($estudiante)
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="student-header">
                <h5><i class="bi bi-person-circle me-2"></i>{{ optional($estudiante->usuario)->nombre ?? 'Desconocido' }} {{ optional($estudiante->usuario)->apellido ?? '' }}</h5>
                <p><i class="bi bi-mortarboard me-1"></i> {{ optional($estudiante->curso)->nombre ?? 'Sin curso' }} | Código: {{ $estudiante->codigo_estudiante ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-left-color: {{ ($promedioGeneral ?? 0) >= 70 ? '#2e7d32' : '#c62828' }};">
                <div class="label">Promedio General</div>
                <div class="number" style="color: {{ ($promedioGeneral ?? 0) >= 70 ? '#2e7d32' : '#c62828' }};">
                    {{ $promedioGeneral ? number_format($promedioGeneral, 1) : '-' }}
                </div>
                @if($promedioGeneral)
                <div class="mt-1">
                    @if($promedioGeneral >= 70)
                    <span class="badge rounded-pill bg-success">Aprobado</span>
                    @elseif($promedioGeneral >= 40)
                    <span class="badge rounded-pill" style="background:#e65100;">En riesgo</span>
                    @else
                    <span class="badge rounded-pill bg-danger">Reprobado</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clipboard-data me-2"></i>Calificaciones</span>
            <span class="badge bg-light text-dark">{{ $calificaciones->count() }} registros</span>
        </div>
        <div class="card-body">
            @if($calificaciones->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size:2rem;color:#ddd;"></i>
                    <p class="mt-2">No hay calificaciones registradas para esta asignación.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Actividad</th><th>Período</th><th>Nota</th><th>Nota Máxima</th><th>Fecha</th></tr></thead>
                        <tbody>
                            @foreach($calificaciones as $c)
                            <tr>
                                <td><strong>{{ $c->tipoEvaluacion->nombre ?? 'N/A' }}</strong></td>
                                <td><span class="badge rounded-pill bg-secondary">{{ $c->periodo->nombre ?? 'N/A' }}</span></td>
                                <td><span class="grade-badge {{ $c->nota >= 70 ? 'grade-high' : ($c->nota >= 40 ? 'grade-mid' : 'grade-low') }}">{{ number_format($c->nota, 1) }}</span></td>
                                <td>{{ $c->nota_maxima }}</td>
                                <td>{{ $c->fecha_evaluacion ? $c->fecha_evaluacion->format('d/m/Y') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card card-custom mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-send me-2"></i>Citaciones</span>
            <span class="badge bg-light text-dark">{{ $citaciones->count() }} registros</span>
        </div>
        <div class="card-body">
            @if($citaciones->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size:2rem;color:#ddd;"></i>
                    <p class="mt-2">No hay citaciones registradas para este estudiante.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Título</th><th>Tipo</th><th>Estado</th><th>Fecha de Creación</th></tr></thead>
                        <tbody>
                            @foreach($citaciones as $c)
                            <tr>
                                <td><strong>{{ $c->titulo }}</strong></td>
                                <td><span class="badge rounded-pill bg-info">{{ ucfirst($c->tipo) }}</span></td>
                                <td>
                                    @if($c->estado === 'pendiente')
                                    <span class="badge rounded-pill bg-warning text-dark">Pendiente</span>
                                    @elseif($c->estado === 'enviada')
                                    <span class="badge rounded-pill bg-success">Enviada</span>
                                    @else
                                    <span class="badge rounded-pill bg-secondary">{{ ucfirst($c->estado) }}</span>
                                    @endif
                                </td>
                                <td>{{ $c->created_at ? $c->created_at->format('d/m/Y') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
const BASE = '{{ url("docente/reportes/estudiante") }}';
let currentAsig = '{{ $asignacionId ?? "" }}';

function navigateAsignacion(val) {
    currentAsig = val;
    if (val) { window.location.href = BASE + '/0?asignacion=' + val; }
}

function navigateEstudiante(val) {
    if (val && currentAsig) { window.location.href = BASE + '/' + val + '?asignacion=' + currentAsig; }
}

function generateReport() {
    const est = document.getElementById('selEstudiante').value;
    if (est && currentAsig) { window.location.href = BASE + '/' + est + '?asignacion=' + currentAsig; }
    else { alert('Seleccione asignación y estudiante para generar el reporte.'); }
}
</script>
@endsection
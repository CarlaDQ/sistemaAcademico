@extends('layouts.app')

@section('title', 'Reporte de Curso - Docente')

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
    .table th {
        background: #f4f6f9; border-bottom: 2px solid #1e3c72;
        color: #1e3c72; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.3px;
    }
    .table td { vertical-align: middle; font-size: 0.85rem; }
    .table tbody tr:hover { background: #f8faff; }
    .table tbody tr:nth-child(even) { background: #f8faff; }
    .grade-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
    .grade-high { background: #e8f5e9; color: #2e7d32; }
    .grade-mid { background: #fff3e0; color: #e65100; }
    .grade-low { background: #ffebee; color: #c62828; }
    .stat-card {
        background: #fff; border-radius: 12px; padding: 22px;
        height: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .stat-card .number { font-size: 2rem; font-weight: 700; }
    .stat-card .label { font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .form-select:focus { border-color: #1e3c72; box-shadow: 0 0 0 2px rgba(30,60,114,0.15); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1e3c72;"><i class="bi bi-people-fill me-2"></i>Reporte por Curso</h3>
            <p class="text-muted small mb-0">Rendimiento general del curso con estadísticas y detalle por estudiante</p>
        </div>
        <a href="{{ route('docente.reportes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card card-custom mb-4">
        <div class="card-header"><i class="bi bi-funnel me-2"></i>Seleccionar Asignación</div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-semibold"><i class="bi bi-book me-1"></i> Asignación</label>
                    <select class="form-select" onchange="if(this.value) window.location.href='{{ url("docente/reportes/curso") }}/0?asignacion='+this.value">
                        <option value="">Seleccionar asignación...</option>
                        @foreach($asignaciones as $a)
                            <option value="{{ $a->id_asignacion }}" {{ $a->id_asignacion == $asignacionId ? 'selected' : '' }}>
                                [{{ $a->codigo ?? '' }}] {{ optional($a->materia)->nombre ?? 'N/A' }} - {{ optional($a->curso)->nombre ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">&nbsp;</label>
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Seleccione una asignación para ver el reporte
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($asignacion)
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="border-left: 5px solid #1e3c72;">
                <div class="label">Estudiantes</div>
                <div class="number" style="color:#1e3c72;">{{ $estudiantes->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left: 5px solid #2e7d32;">
                <div class="label">Promedio del Curso</div>
                <div class="number" style="color:#2e7d32;">{{ $promedioCurso ? number_format($promedioCurso, 1) : '-' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left: 5px solid #2e7d32;">
                <div class="label">Aprobados (&ge; 70)</div>
                <div class="number" style="color:#2e7d32;">{{ $aprobados }}</div>
                @if($estudiantes->count() > 0)
                <div class="mt-1"><small class="text-muted">{{ $estudiantes->count() > 0 ? number_format(($aprobados / $estudiantes->count()) * 100, 1) : 0 }}% del total</small></div>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left: 5px solid #c62828;">
                <div class="label">Reprobados (&lt; 70)</div>
                <div class="number" style="color:#c62828;">{{ $reprobados }}</div>
                @if($estudiantes->count() > 0)
                <div class="mt-1"><small class="text-muted">{{ $estudiantes->count() > 0 ? number_format(($reprobados / $estudiantes->count()) * 100, 1) : 0 }}% del total</small></div>
                @endif
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list me-2"></i>{{ optional($asignacion->materia)->nombre ?? 'N/A' }} — {{ optional($asignacion->curso)->nombre ?? 'N/A' }}</span>
            <span class="badge bg-light text-dark px-3 py-2">{{ $estudiantes->count() }} estudiantes</span>
        </div>
        <div class="card-body p-0">
            @if($estudiantes->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people" style="font-size:2.5rem;color:#ddd;"></i>
                    <p class="mt-2">No hay estudiantes registrados en este curso.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>#</th><th>Estudiante</th><th>Actividades</th><th>Promedio</th><th>Estado</th><th style="width:80px;">Acción</th></tr></thead>
                        <tbody>
                            @foreach($estudiantes as $idx => $e)
                            @php $prom = $e->promedio ?? 0; @endphp
                            <tr>
                                <td class="text-muted">{{ $idx + 1 }}</td>
                                <td><strong>{{ optional($e->usuario)->nombre ?? 'Desconocido' }} {{ optional($e->usuario)->apellido ?? '' }}</strong></td>
                                <td><span class="badge rounded-pill bg-secondary">{{ $e->actividades_count }}</span></td>
                                <td>
                                    <span class="grade-badge {{ $prom >= 70 ? 'grade-high' : ($prom >= 40 ? 'grade-mid' : 'grade-low') }}">
                                        {{ $prom ? number_format($prom, 1) : '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($prom >= 70)
                                    <span class="badge rounded-pill bg-success px-3 py-2">Aprobado</span>
                                    @elseif($prom > 0)
                                    <span class="badge rounded-pill bg-danger px-3 py-2">Reprobado</span>
                                    @else
                                    <span class="badge rounded-pill bg-secondary px-3 py-2">Sin notas</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('docente.reportes.estudiante', ['estudiante' => $e->id_estudiante, 'asignacion' => $asignacionId]) }}"
                                       class="btn btn-sm btn-outline-primary px-3">
                                        <i class="bi bi-eye me-1"></i> Ver
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @elseif($asignacionId)
    <div class="text-center py-5 text-muted">
        <i class="bi bi-exclamation-circle" style="font-size:3rem;color:#ddd;"></i>
        <p class="mt-3">No se encontró la asignación seleccionada. Puede que haya sido desactivada o eliminada.</p>
        <a href="{{ route('docente.reportes.index') }}" class="btn btn-outline-secondary btn-sm mt-2">
            <i class="bi bi-arrow-left me-1"></i> Volver a reportes
        </a>
    </div>
    @endif
</div>
@endsection
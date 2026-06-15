@extends('layouts.app')

@section('title', ($hijo->usuario->nombre ?? '') . ' ' . ($hijo->usuario->apellido ?? '') . ' - Detalle')

@push('styles')
<style>
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .profile-header {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        border-radius: 16px;
        padding: 32px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(30,60,114,0.3);
    }
    .profile-header .avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        border: 3px solid rgba(255,255,255,0.4);
    }
    .card-custom {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: none;
        margin-bottom: 24px;
    }
    .card-custom .card-header {
        background: #f8f9fa;
        border-radius: 12px 12px 0 0;
        padding: 14px 20px;
        font-weight: 600;
        border-bottom: 2px solid #1e3c72;
    }
    .nav-tabs .nav-link {
        color: #555;
        font-weight: 500;
        border: none;
        padding: 10px 20px;
    }
    .nav-tabs .nav-link.active {
        color: #1e3c72;
        border-bottom: 3px solid #1e3c72;
        background: transparent;
    }
    .nav-tabs .nav-link:hover { color: #1e3c72; }
    .summary-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .summary-card .number { font-size: 1.8rem; font-weight: 700; }
    .summary-card .label { font-size: 0.85rem; color: #666; }
    .stat-grade { border-left: 4px solid #1e3c72; }
    .stat-attendance { border-left: 4px solid #28a745; }
    .stat-pending { border-left: 4px solid #ffc107; }
    .table th { background: #f8f9fa; border-bottom: 2px solid #1e3c72; }
    .table td { vertical-align: middle; }
    .table tr:nth-child(even) { background: #f8faff; }
    .badge-citacion { background: #e3f2fd; color: #1565c0; }
    .badge-aviso { background: #fff3e0; color: #e65100; }
    .badge-comunicado { background: #e8f5e9; color: #2e7d32; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
<div class="profile-header d-flex align-items-center gap-4">
    <div class="avatar flex-shrink-0">
        {{ substr($hijo->usuario->nombre ?? '?', 0, 1) }}{{ substr($hijo->usuario->apellido ?? '', 0, 1) }}
    </div>
    <div class="flex-grow-1">
        <h3 class="mb-1">{{ $hijo->usuario->nombre }} {{ $hijo->usuario->apellido }}</h3>
        <p class="mb-0 opacity-75">
            <i class="bi bi-mortarboard me-1"></i> {{ $hijo->curso->nombre ?? 'Sin curso' }}
            &nbsp;|&nbsp; <i class="bi bi-book me-1"></i> {{ $calificaciones->count() }} calificaciones
        </p>
    </div>
    <a href="{{ route('padre.permiso.crear', $hijo->id_estudiante) }}" class="btn btn-warning text-dark fw-bold">
        <i class="bi bi-file-earmark-text me-1"></i> Solicitar Permiso
    </a>
</div>

    <ul class="nav nav-tabs mb-4" id="detailTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button">
                <i class="bi bi-journal-text me-1"></i> Calificaciones
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button">
                <i class="bi bi-calendar-check me-1"></i> Asistencia
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="notices-tab" data-bs-toggle="tab" data-bs-target="#notices" type="button">
                <i class="bi bi-envelope-paper me-1"></i> Citaciones
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button">
                <i class="bi bi-clipboard-data me-1"></i> Resumen
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="grades">
            <div class="row">
                <div class="col-md-9">
                    <div class="card card-custom">
                        <div class="card-header">
                            <i class="bi bi-journal-text me-2"></i>Calificaciones por Materia
                        </div>
                        <div class="card-body">
                            @forelse($materias as $materia)
                                <div class="mb-4">
                                    <h6 class="fw-bold" style="border-left: 4px solid #1e3c72; padding-left: 12px;">
                                        {{ $materia['asignacion']->materia->nombre ?? 'Materia' }}
                                        <span class="float-end text-primary">Prom: <strong>{{ number_format($materia['promedio'], 1) }}</strong></span>
                                    </h6>
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Actividad</th>
                                                <th>Periodo</th>
                                                <th>Nota</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($materia['calificaciones'] as $c)
                                                <tr>
                                                    <td>{{ $c->tipoEvaluacion->nombre ?? '' }}</td>
                                                    <td>{{ $c->periodo->nombre ?? '' }}</td>
                                                    <td>
                                                        <span class="badge rounded-pill
                                                            {{ $c->nota >= 70 ? 'bg-success' : ($c->nota >= 40 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                            {{ $c->nota }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @empty
                                <p class="text-muted text-center py-3">Sin calificaciones registradas.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom">
                        <div class="card-header">
                            <i class="bi bi-bar-chart me-2"></i>Rendimiento
                        </div>
                        <div class="card-body">
                            <canvas id="hijoGradesChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="attendance">
            <div class="row">
                <div class="col-md-9">
                    <div class="card card-custom">
                        <div class="card-header">
                            <i class="bi bi-calendar-check me-2"></i>Registro de Asistencia
                        </div>
                        <div class="card-body">
                            @if($asistencias->isNotEmpty())
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Materia</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($asistencias as $idx => $a)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $a->asignacion->materia->nombre ?? 'N/A' }}</td>
                                                <td>{{ $a->fecha ? $a->fecha->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    <span class="badge rounded-pill                                                                                                                                                                                                                                                                    {{ $a->estado == 'presente' ? 'bg-success' : ($a->estado == 'ausente' ? 'bg-danger' : ($a->estado == 'tardanza' ? 'bg-warning text-dark' : ($a->estado == 'permiso' ? 'bg-info' : 'bg-info'))) }}">
                                                        {{ ucfirst($a->estado) }}
                                                    </span>
                                                    @if($a->observacion)
                                                        <br><small class="text-muted">{{ $a->observacion }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted text-center py-3">Sin registros de asistencia.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-custom">
                        <div class="card-header">
                            <i class="bi bi-pie-chart me-2"></i>Distribuci&oacute;n
                        </div>
                        <div class="card-body">
                            <canvas id="hijoAttendanceChart" height="250"></canvas>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span>Presente:</span>
                                <strong>{{ $pctPresente }}%</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Ausente:</span>
                                <strong>{{ $pctAusente }}%</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Tardanza:</span>
                                <strong>{{ $pctTardanza }}%</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Permiso:</span>
                                <strong>{{ $pctPermiso }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="notices">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-envelope-paper me-2"></i>Citaciones y Notificaciones
                </div>
                <div class="card-body">
                    @if($citaciones->isNotEmpty())
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>T&iacute;tulo</th>
                                    <th>Docente</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($citaciones as $idx => $c)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><strong>{{ $c->titulo }}</strong></td>
                                        <td>{{ $c->docente->usuario->nombre ?? '' }} {{ $c->docente->usuario->apellido ?? '' }}</td>
                                        <td>
                                            <span class="badge rounded-pill
                                                {{ $c->tipo == 'citacion' ? 'badge-citacion' : ($c->tipo == 'aviso' ? 'badge-aviso' : 'badge-comunicado') }}">
                                                {{ ucfirst($c->tipo) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill
                                                {{ $c->estado == 'pendiente' ? 'bg-warning text-dark' : 'bg-success' }}">
                                                {{ ucfirst($c->estado) }}
                                            </span>
                                        </td>
                                        <td>{{ $c->created_at ? $c->created_at->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted text-center py-3">No hay citaciones registradas.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="summary">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="summary-card stat-grade">
                        <div class="number text-primary">{{ $promedioGeneral }}</div>
                        <div class="label">Promedio General</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card stat-attendance">
                        <div class="number text-success">{{ $pctPresente }}%</div>
                        <div class="label">Asistencia</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card stat-pending">
                        <div class="number text-warning">{{ $citaciones->where('estado', 'pendiente')->count() }}</div>
                        <div class="label">Citaciones Pendientes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('hijoGradesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_map(function($m) { return $m['asignacion']->materia->nombre ?? 'N/A'; }, $materias)) !!},
            datasets: [{
                label: 'Promedio',
                data: {!! json_encode(array_map(function($m) { return round($m['promedio'], 1); }, $materias)) !!},
                backgroundColor: ['rgba(30,60,114,0.7)', 'rgba(52,168,83,0.7)', 'rgba(251,188,4,0.7)', 'rgba(234,67,53,0.7)'],
                borderColor: ['#1e3c72', '#34a853', '#fbbc04', '#ea4335'],
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    new Chart(document.getElementById('hijoAttendanceChart'), {
        type: 'doughnut',
        data: {
            labels: ['Presente', 'Ausente', 'Tardanza', 'Justificado', 'Permiso'],
            datasets: [{
                data: [{{ $presentes }}, {{ $ausentes }}, {{ $tardanzas }}, {{ $justificados }}, {{ $permisos }}],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6c757d'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } }
            },
            cutout: '60%',
        }
    });
</script>
@endpush
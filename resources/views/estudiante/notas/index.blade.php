@extends('layouts.app')

@section('title', 'Mis Notas - Estudiante')

@push('styles')
<style>
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .card-custom {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: none;
        margin-bottom: 24px;
    }
    .card-custom .card-header {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        color: #fff;
        border-radius: 12px 12px 0 0;
        padding: 16px 24px;
        font-weight: 600;
    }
    .table th { background: #f8f9fa; border-bottom: 2px solid #1e3c72; }
    .table td { vertical-align: middle; }
    .table tr:nth-child(even) { background: #f8faff; }
    .promedio-card {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        color: #fff;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
    }
    .promedio-card .valor { font-size: 2.5rem; font-weight: 700; }
    .promedio-card .label { opacity: 0.85; font-size: 0.9rem; }
    .subject-section { margin-bottom: 24px; }
    .subject-title {
        background: #f8f9fa;
        padding: 10px 16px;
        border-radius: 8px 8px 0 0;
        border-left: 4px solid #1e3c72;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-9">
            <div class="card card-custom">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Mis Calificaciones</h4>
                </div>
                <div class="card-body">
                    @forelse($materias as $materia)
                        <div class="subject-section">
                            <div class="subject-title">
                                {{ $materia['asignacion']->materia->nombre ?? 'Materia' }}
                                <small class="text-muted">({{ $materia['asignacion']->curso->nombre ?? '' }})</small>
                                <span class="float-end text-primary">Prom: <strong>{{ number_format($materia['promedio'], 1) }}</strong></span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Actividad</th>
                                            <th>Periodo</th>
                                            <th>Nota</th>
                                            <th>Nota M&aacute;x.</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materia['calificaciones'] as $c)
                                            <tr>
                                                <td>{{ $c->tipoEvaluacion->nombre ?? 'N/A' }}</td>
                                                <td>{{ $c->periodo->nombre ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge rounded-pill
                                                        {{ $c->nota >= 70 ? 'bg-success' : ($c->nota >= 40 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                        {{ $c->nota }}
                                                    </span>
                                                </td>
                                                <td>{{ $c->nota_maxima }}</td>
                                                <td>{{ $c->fecha_evaluacion ? $c->fecha_evaluacion->format('d/m/Y') : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x" style="font-size: 2rem;"></i>
                            <p class="mt-2">No hay calificaciones registradas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="promedio-card mb-4">
                <div class="label">Promedio General</div>
                <div class="valor">{{ number_format($promedioGeneral, 1) }}</div>
                <div class="label">/ 100</div>
            </div>

            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Rendimiento</h5>
                </div>
                <div class="card-body">
                    <canvas id="notasChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('notasChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($materias->pluck('asignacion.materia.nombre')->map(function($n) { return $n ?? 'N/A'; })) !!},
            datasets: [{
                label: 'Promedio',
                data: {!! json_encode($materias->pluck('promedio')->map(function($v) { return round($v, 1); })) !!},
                backgroundColor: [
                    'rgba(30,60,114,0.7)',
                    'rgba(52,168,83,0.7)',
                    'rgba(251,188,4,0.7)',
                    'rgba(234,67,53,0.7)',
                    'rgba(154,66,244,0.7)',
                ],
                borderColor: [
                    '#1e3c72', '#34a853', '#fbbc04', '#ea4335', '#9a42f4'
                ],
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
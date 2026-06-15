@extends('layouts.app')

@section('title', 'Mi Asistencia - Estudiante')

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
    .stat-card {
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        color: #fff;
    }
    .stat-card .number { font-size: 2rem; font-weight: 700; }
    .stat-card .label { opacity: 0.85; font-size: 0.85rem; }
    .stat-presente { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-ausente { background: linear-gradient(135deg, #dc3545, #e74c3c); }
    .stat-tardanza { background: linear-gradient(135deg, #ffc107, #fd7e14); }
    .stat-justificado { background: linear-gradient(135deg, #17a2b8, #0dcaf0); }
    .badge-presente { background: #d4edda; color: #155724; }
    .badge-ausente { background: #f8d7da; color: #721c24; }
    .badge-tardanza { background: #fff3cd; color: #856404; }
    .badge-justificado { background: #d1ecf1; color: #0c5460; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-9">
            <div class="card card-custom">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Mi Asistencia</h4>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:8px;padding:6px 16px;font-weight:600;color:#fff;"><i class="bi bi-funnel"></i> Filtrar</button>
                            <a href="{{ route('estudiante.asistencia') }}" class="btn btn-outline-secondary ms-2">Limpiar</a>
                        </div>
                    </form>

                    @if($asistencias->isNotEmpty())
                        <div class="table-responsive">
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
                                                <span class="badge rounded-pill
                                                    {{ $a->estado == 'presente' ? 'badge-presente' : ($a->estado == 'ausente' ? 'badge-ausente' : ($a->estado == 'tardanza' ? 'badge-tardanza' : 'badge-justificado')) }}">
                                                    <i class="bi bi-{{ $a->estado == 'presente' ? 'check-circle' : ($a->estado == 'ausente' ? 'x-circle' : ($a->estado == 'tardanza' ? 'clock' : 'file-text')) }}"></i>
                                                    {{ ucfirst($a->estado) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                            <p class="mt-2">No hay registros de asistencia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-presente mb-3">
                <div class="number">{{ $porcentajePresente }}%</div>
                <div class="label">Presente ({{ $presentes }})</div>
            </div>
            <div class="stat-card stat-ausente mb-3">
                <div class="number">{{ $porcentajeAusente }}%</div>
                <div class="label">Ausente ({{ $ausentes }})</div>
            </div>
            <div class="stat-card stat-tardanza mb-3">
                <div class="number">{{ $porcentajeTardanza }}%</div>
                <div class="label">Tardanza ({{ $tardanzas }})</div>
            </div>
            <div class="stat-card stat-justificado mb-3">
                <div class="number">{{ $porcentajeJustificado }}%</div>
                <div class="label">Justificado ({{ $justificados }})</div>
            </div>

            <div class="card card-custom">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Distribuci&oacute;n</h5>
                </div>
                <div class="card-body">
                    <canvas id="asistenciaChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('asistenciaChart'), {
        type: 'doughnut',
        data: {
            labels: ['Presente', 'Ausente', 'Tardanza', 'Justificado'],
            datasets: [{
                data: [{{ $presentes }}, {{ $ausentes }}, {{ $tardanzas }}, {{ $justificados }}],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#17a2b8'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 12, usePointStyle: true }
                }
            },
            cutout: '60%',
        }
    });
</script>
@endpush
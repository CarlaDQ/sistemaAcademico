@extends('layouts.app')

@section('title', 'Dashboard - U.E. David Pinilla')

@push('styles')
<style>
    .stat-card { border-radius: 14px; border: none; transition: all 0.3s; overflow: hidden; position: relative; }
    .stat-card::after { content: ''; position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.08); }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    .stat-card .stat-icon { font-size: 2rem; opacity: 0.85; }
    .stat-card .stat-number { font-size: 2rem; font-weight: 700; line-height: 1.2; }
    .stat-card .stat-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85; }

    .horario-table th { background: #1e3c72; color: #fff; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 4px; text-align: center; border: 1px solid #2a4a7f; }
    .horario-table td { text-align: center; vertical-align: middle; padding: 4px; border: 1px solid #dee2e6; height: 50px; font-size: 0.7rem; }
    .horario-table .hora-col { font-weight: 700; color: #1e3c72; width: 55px; font-size: 0.75rem; }

    .attendance-stat { text-align: center; padding: 16px 8px; border-radius: 12px; }
    .attendance-stat .num { font-size: 1.8rem; font-weight: 700; }
    .attendance-stat .lbl { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.3px; opacity: 0.8; }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3><i class="bi bi-speedometer2 me-2"></i>Dashboard</h3>
        <p>Panel principal de control del sistema</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background: linear-gradient(135deg, #1e3c72, #2a4a7f);">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-mortarboard-fill stat-icon me-3"></i>
                <div>
                    <div class="stat-number">{{ $totalEstudiantes }}</div>
                    <div class="stat-label">Estudiantes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background: linear-gradient(135deg, #2e7d32, #388e3c);">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-person-badge-fill stat-icon me-3"></i>
                <div>
                    <div class="stat-number">{{ $totalDocentes }}</div>
                    <div class="stat-label">Docentes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background: linear-gradient(135deg, #1565c0, #1976d2);">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-book-fill stat-icon me-3"></i>
                <div>
                    <div class="stat-number">{{ $totalCursos }}</div>
                    <div class="stat-label">Cursos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background: linear-gradient(135deg, #6a1b9a, #8e24aa);">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-people-fill stat-icon me-3"></i>
                <div>
                    <div class="stat-number">{{ $totalPadres }}</div>
                    <div class="stat-label">Padres</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 mb-4">
    <div class="col-md-2">
        <a href="{{ route('admin.usuarios.create') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border:none;border-radius:12px;font-weight:600;">
            <i class="bi bi-person-plus-fill d-block mb-1" style="font-size:1.3rem;"></i>
            <small>Nuevo Usuario</small>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.estudiantes.create') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#2e7d32,#388e3c);color:#fff;border:none;border-radius:12px;font-weight:600;">
            <i class="bi bi-mortarboard-fill d-block mb-1" style="font-size:1.3rem;"></i>
            <small>Nuevo Estudiante</small>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.asignaciones.index') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#e65100,#ef6c00);color:#fff;border:none;border-radius:12px;font-weight:600;">
            <i class="bi bi-journal-bookmark-fill d-block mb-1" style="font-size:1.3rem;"></i>
            <small>Asignaciones</small>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.horarios.index') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#6a1b9a,#8e24aa);color:#fff;border:none;border-radius:12px;font-weight:600;">
            <i class="bi bi-calendar-week-fill d-block mb-1" style="font-size:1.3rem;"></i>
            <small>Horarios</small>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.reportes.index') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#00838f,#0097a7);color:#fff;border:none;border-radius:12px;font-weight:600;">
            <i class="bi bi-file-earmark-pdf-fill d-block mb-1" style="font-size:1.3rem;"></i>
            <small>Reportes</small>
        </a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.usuarios.index') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#37474f,#455a64);color:#fff;border:none;border-radius:12px;font-weight:600;">
            <i class="bi bi-people-fill d-block mb-1" style="font-size:1.3rem;"></i>
            <small>Usuarios</small>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
            <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:14px 20px;font-weight:600;border:none;">
                <i class="bi bi-calendar-check me-2"></i>Asistencia Hoy
            </div>
            <div class="card-body">
                @if($totalAsistenciaHoy > 0)
                <div class="row g-2">
                    <div class="col-3">
                        <div class="attendance-stat" style="background:#e8f5e9;">
                            <div class="num text-success">{{ $presentesHoy }}</div>
                            <div class="lbl text-success">Presentes</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="attendance-stat" style="background:#fff3e0;">
                            <div class="num text-warning">{{ $tardanzasHoy }}</div>
                            <div class="lbl text-warning">Tardanzas</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="attendance-stat" style="background:#ffebee;">
                            <div class="num text-danger">{{ $ausentesHoy }}</div>
                            <div class="lbl text-danger">Ausentes</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="attendance-stat" style="background:#f4f6f9;">
                            <div class="num" style="color:#1e3c72;">{{ $totalAsistenciaHoy }}</div>
                            <div class="lbl" style="color:#1e3c72;">Total</div>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x" style="font-size:2rem;color:#ddd;"></i>
                    <p class="mt-2 mb-0">No hay registros de asistencia para hoy.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);height:100%;">
            <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:14px 20px;font-weight:600;border:none;">
                <i class="bi bi-bar-chart me-2"></i>Promedio General
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="font-size:3rem;font-weight:700;color:{{ $promedioGeneral && $promedioGeneral >= 70 ? '#2e7d32' : '#e65100' }};">
                    {{ $promedioGeneral ? number_format($promedioGeneral, 1) : '—' }}
                </div>
                <div class="text-muted small mt-1">de 100 puntos</div>
                @if($promedioGeneral)
                <div class="mt-2">
                    <span class="badge rounded-pill px-3 py-2" style="background:{{ $promedioGeneral >= 70 ? '#2e7d32' : '#e65100' }};">
                        {{ $promedioGeneral >= 70 ? 'Aprobado' : 'En riesgo' }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);height:100%;">
            <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:14px 20px;font-weight:600;border:none;">
                <i class="bi bi-pie-chart me-2"></i>Estudiantes por Curso
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartEstudiantesCurso" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);margin-bottom:24px;">
    <div class="card-header d-flex justify-content-between align-items-center" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:14px 20px;font-weight:600;border:none;">
        <span><i class="bi bi-calendar-week me-2"></i>Horario Semanal</span>
        <span class="badge bg-light text-dark px-3 py-2" style="font-weight:500;">Lun a Sab · 14:00 - 17:50</span>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="horario-table table mb-0">
                <thead>
                    <tr>
                        <th>Hora</th>
                        @foreach(['LUN','MAR','MIE','JUE','VIE','SAB'] as $d)
                        <th>{{ $d }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $diasShort = ['lunes','martes','miercoles','jueves','viernes','sabado']; $franjas = ['14:00','14:40','15:20','16:30','17:10']; @endphp
                    @foreach($franjas as $idx => $franja)
                    <tr>
                        <td class="hora-col">{{ $franja }}</td>
                        @foreach($diasShort as $d)
                        <td>
                            @php
                                $clases = $horarioGrid[$d]->filter(function($h) use ($franja) {
                                    return substr($h->hora_inicio, 0, 5) === $franja;
                                });
                            @endphp
                            @forelse($clases as $c)
                            <div style="font-size:0.65rem;line-height:1.3;">
                                <strong style="color:#1e3c72;">{{ $c->asignacion->codigo }}</strong>
                                <div class="text-muted">{{ $c->asignacion->materia->nombre ?? '' }}</div>
                                <div style="color:#999;">{{ $c->asignacion->docente->usuario->nombre ?? '' }}</div>
                            </div>
                            @empty
                            <span class="text-muted" style="font-size:0.7rem;">—</span>
                            @endforelse
                        </td>
                        @endforeach
                    </tr>
                    @if($idx === 2)
                    <tr style="background:#fff8e1;">
                        <td class="hora-col" style="color:#e65100;">16:00</td>
                        <td colspan="6" style="color:#e65100;font-weight:600;font-size:0.75rem;">
                            <i class="bi bi-cup-hot me-1"></i> RECREO · 16:00 - 16:30
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i> Cada periodo dura 40 min. Recreo de 16:00 a 16:30.</p>
    </div>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:14px 20px;font-weight:600;border:none;">
        <i class="bi bi-activity me-2"></i>Actividad Reciente
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f4f6f9;">
                    <tr style="border-bottom:2px solid #1e3c72;">
                        <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.3px;padding:10px 16px;">Usuario</th>
                        <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.3px;padding:10px 16px;">Email</th>
                        <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.3px;padding:10px 16px;">Rol</th>
                        <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.3px;padding:10px 16px;">Creado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recientes as $r)
                    <tr>
                        <td class="fw-bold" style="color:#1e3c72;">{{ $r->nombre }}</td>
                        <td class="text-muted small">{{ $r->email }}</td>
                        <td><span class="badge rounded-pill px-3 py-2" style="background:#1e3c72;">{{ $r->rol }}</span></td>
                        <td class="text-muted small">{{ $r->creado ? $r->creado->diffForHumans() : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">Sin actividad reciente.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartEstudiantesCurso'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($estudiantesPorCurso->pluck('label')) !!},
        datasets: [{
            data: {!! json_encode($estudiantesPorCurso->pluck('count')) !!},
            backgroundColor: ['#1e3c72','#2e7d32','#e65100','#1565c0','#6a1b9a','#00897b','#f9a825','#c62828'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        cutout: '55%',
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 }, padding: 12 } }
        }
    }
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'Dashboard - Docente')

@push('styles')
<style>
    .stat-card { border-radius: 14px; border: none; transition: all 0.3s; overflow: hidden; position: relative; }
    .stat-card::after { content: ''; position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.08); }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    .stat-card .stat-icon { font-size: 1.8rem; opacity: 0.85; }
    .stat-card .stat-number { font-size: 1.8rem; font-weight: 700; line-height: 1.2; }
    .stat-card .stat-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85; }
    .subject-badge { background: #e8f0fe; color: #1e3c72; border-radius: 50px; padding: 6px 14px; font-size: 0.8rem; font-weight: 600; display: inline-block; margin: 2px; }
    .quick-btn { border-radius: 12px; border: none; padding: 14px 8px; font-weight: 600; font-size: 0.85rem; transition: all 0.3s; }
    .quick-btn:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;"><i class="bi bi-speedometer2 me-2"></i>Panel del Docente</h3>
        <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Bienvenido, {{ $docente->usuario->nombre ?? 'Docente' }}</p>
    </div>
    <span class="badge bg-light text-dark px-3 py-2" style="border:1px solid #ddd;border-radius:20px;">
        <i class="bi bi-bookmarks me-1" style="color:#1e3c72;"></i> {{ $totalMaterias }} materias asignadas
    </span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-journal-bookmark-fill stat-icon me-3"></i>
                <div>
                    <div class="stat-number">{{ $totalMaterias }}</div>
                    <div class="stat-label">Materias</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background:linear-gradient(135deg,#2e7d32,#388e3c);">
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
        <div class="card stat-card text-white" style="background:linear-gradient(135deg,#e65100,#ef6c00);">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-envelope-paper-fill stat-icon me-3"></i>
                <div>
                    <div class="stat-number">{{ $citacionesPendientes }}</div>
                    <div class="stat-label">Citaciones Pend.</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background:linear-gradient(135deg,#6a1b9a,#8e24aa);">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-people-fill stat-icon me-3"></i>
                <div>
                    <div class="stat-number">{{ $totalCursos }}</div>
                    <div class="stat-label">Cursos</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 mb-4">
    <div class="col-md-3">
        <a href="{{ route('docente.calificaciones.index') }}" class="quick-btn btn w-100 text-white" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);">
            <i class="bi bi-table d-block mb-1" style="font-size:1.5rem;"></i> Calificaciones
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('docente.asistencia.index') }}" class="quick-btn btn w-100 text-white" style="background:linear-gradient(135deg,#2e7d32,#388e3c);">
            <i class="bi bi-calendar-check d-block mb-1" style="font-size:1.5rem;"></i> Asistencia
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('docente.citaciones.index') }}" class="quick-btn btn w-100 text-white" style="background:linear-gradient(135deg,#e65100,#ef6c00);">
            <i class="bi bi-envelope-paper d-block mb-1" style="font-size:1.5rem;"></i> Citaciones
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('docente.reportes.index') }}" class="quick-btn btn w-100 text-white" style="background:linear-gradient(135deg,#6a1b9a,#8e24aa);">
            <i class="bi bi-file-earmark-pdf d-block mb-1" style="font-size:1.5rem;"></i> Reportes
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
            <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:14px 20px;font-weight:600;border:none;">
                <i class="bi bi-collection me-2"></i>Mis Materias por Curso
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f4f6f9;">
                            <tr style="border-bottom:2px solid #1e3c72;">
                                <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;padding:10px 16px;">Curso</th>
                                <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;padding:10px 16px;">Estudiantes</th>
                                <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;padding:10px 16px;">Materias</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cursosData as $cd)
                            <tr>
                                <td class="fw-bold" style="color:#1e3c72;">{{ $cd['nombre'] }}</td>
                                <td><span class="badge bg-primary rounded-pill px-3 py-2">{{ $cd['estudiantes'] }}</span></td>
                                <td>
                                    @foreach(explode(', ', $cd['materias']) as $mat)
                                    <span class="subject-badge">{{ $mat }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No tienes materias asignadas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);height:100%;">
            <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:14px 20px;font-weight:600;border:none;">
                <i class="bi bi-calendar-check me-2"></i>Asistencia Hoy
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                @if($presentes + $ausentes + $tardanzas > 0)
                <div class="text-center mb-3">
                    <div style="font-size:2.5rem;font-weight:700;color:#1e3c72;">{{ $presentes + $ausentes + $tardanzas }}</div>
                    <div class="text-muted small">registros hoy</div>
                </div>
                <div class="d-flex justify-content-around text-center">
                    <div>
                        <div class="fw-bold text-success" style="font-size:1.3rem;">{{ $presentes }}</div>
                        <div class="text-muted small">Presentes</div>
                    </div>
                    <div>
                        <div class="fw-bold text-warning" style="font-size:1.3rem;">{{ $tardanzas }}</div>
                        <div class="text-muted small">Tardanzas</div>
                    </div>
                    <div>
                        <div class="fw-bold text-danger" style="font-size:1.3rem;">{{ $ausentes }}</div>
                        <div class="text-muted small">Ausentes</div>
                    </div>
                </div>
                @else
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-calendar-x" style="font-size:2rem;color:#ddd;"></i>
                    <p class="mt-2 mb-0 small">Sin registro hoy</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:14px 20px;font-weight:600;border:none;">
        <i class="bi bi-envelope-paper me-2"></i>Citaciones Recientes
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f4f6f9;">
                    <tr style="border-bottom:2px solid #1e3c72;">
                        <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;padding:10px 16px;">Estudiante</th>
                        <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;padding:10px 16px;">Título</th>
                        <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;padding:10px 16px;">Tipo</th>
                        <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;padding:10px 16px;">Estado</th>
                        <th style="color:#1e3c72;font-size:0.8rem;text-transform:uppercase;padding:10px 16px;">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recientes as $c)
                    <tr>
                        <td class="fw-bold" style="color:#1e3c72;">{{ optional($c->estudiante->usuario)->nombre ?? '' }} {{ optional($c->estudiante->usuario)->apellido ?? '' }}</td>
                        <td>{{ $c->titulo }}</td>
                        <td><span class="badge rounded-pill px-3 py-2" style="background:#e3f2fd;color:#1565c0;">{{ ucfirst($c->tipo) }}</span></td>
                        <td><span class="badge rounded-pill px-3 py-2" style="background:#fff3e0;color:#e65100;">{{ ucfirst($c->estado) }}</span></td>
                        <td class="text-muted small">{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Sin citaciones recientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

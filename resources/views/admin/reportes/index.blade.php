@extends('layouts.app')

@section('title', 'Reportes - U.E. David Pinilla')

@push('styles')
<style>
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .stat-card {
        border-radius: 14px;
        border: none;
        transition: all 0.3s;
        overflow: hidden;
        position: relative;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    .stat-card .card-body { padding: 20px 24px; }
    .stat-card .stat-icon { font-size: 2.2rem; opacity: 0.8; }
    .stat-card .stat-number { font-size: 2rem; font-weight: 700; line-height: 1.2; }
    .stat-card .stat-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.85; }

    .report-card {
        border-radius: 14px;
        border: 1px solid #e8ecf0;
        transition: all 0.3s;
        background: #fff;
        height: 100%;
    }
    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(30,60,114,0.12);
        border-color: #1a73e8;
    }
    .report-card .card-header {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        color: #fff;
        border-radius: 14px 14px 0 0;
        padding: 14px 20px;
        font-weight: 600;
        font-size: 0.95rem;
        border: none;
    }
    .report-card .card-body { padding: 20px; }
    .report-card .card-body p { font-size: 0.85rem; color: #666; margin-bottom: 14px; }

    .table-card {
        border-radius: 14px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .table-card .card-header {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        color: #fff;
        border: none;
        padding: 14px 20px;
        font-weight: 600;
    }
    .table-card .table { margin-bottom: 0; }
    .table-card .table thead th {
        background: #f4f6f9;
        border-bottom: 2px solid #1e3c72;
        color: #1e3c72;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 10px 12px;
    }
    .table-card .table td {
        padding: 10px 12px;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    .table-card .table tbody tr:hover { background: #f8faff; }
    .table-card .table tbody tr:last-child td { border-bottom: none; }

    .horario-table th {
        background: #1e3c72;
        color: #fff;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 4px;
        text-align: center;
        border: 1px solid #2a4a7f;
    }
    .horario-table td {
        text-align: center;
        vertical-align: middle;
        padding: 4px;
        border: 1px solid #dee2e6;
        height: 56px;
        font-size: 0.75rem;
    }
    .horario-table .hora-col {
        font-weight: 700;
        color: #1e3c72;
        width: 55px;
        font-size: 0.75rem;
    }
    .horario-table .receso-row td {
        background: #fff8e1;
        font-weight: 600;
        color: #e65100;
    }
    .horario-badge {
        display: inline-block;
        padding: 3px 6px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 600;
        line-height: 1.3;
        max-width: 100%;
    }
    .horario-badge .codigo { color: #1e3c72; }
    .horario-badge .materia { color: #333; }
    .horario-badge .docente { color: #888; font-size: 0.6rem; }

    .btn-pdf {
        background: #dc3545;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 6px 16px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-pdf:hover { background: #c82333; color: #fff; transform: translateY(-1px); }
    .btn-outline-pdf {
        background: transparent;
        color: #1e3c72;
        border: 1.5px solid #1e3c72;
        border-radius: 8px;
        padding: 6px 16px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-outline-pdf:hover { background: #1e3c72; color: #fff; }
    .form-select, .form-control { border-radius: 8px; font-size: 0.85rem; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1e3c72;"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reportes Académicos</h3>
            <p class="text-muted small mb-0">Gestión de reportes y estadísticas del sistema</p>
        </div>
    </div>

    {{-- Stats row --}}
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
            <div class="card stat-card text-white" style="background: linear-gradient(135deg, #e65100, #ef6c00);">
                <div class="card-body d-flex align-items-center">
                    <i class="bi bi-diagram-3-fill stat-icon me-3"></i>
                    <div>
                        <div class="stat-number">{{ $totalAsignaciones }}</div>
                        <div class="stat-label">Asignaciones</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Report cards --}}
    <div class="row g-3 mb-4">
        {{-- Estudiantes PDF --}}
        <div class="col-md-4">
            <div class="card report-card">
                <div class="card-header"><i class="bi bi-file-earmark-pdf me-2"></i>Listado de Estudiantes</div>
                <div class="card-body">
                    <p><i class="bi bi-people-fill me-1 text-muted"></i> Reporte PDF con todos los estudiantes activos del sistema, incluyendo código, nombres, curso y género.</p>
                    <a href="{{ route('admin.reportes.estudiantes') }}" class="btn btn-pdf" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Generar PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Calificaciones PDF --}}
        <div class="col-md-4">
            <div class="card report-card">
                <div class="card-header"><i class="bi bi-file-earmark-pdf me-2"></i>Calificaciones</div>
                <div class="card-body">
                    <p><i class="bi bi-clipboard-data me-1 text-muted"></i> Reporte PDF de calificaciones por asignación, con notas por período y promedio general.</p>
                    <form id="formCalificaciones" onsubmit="return redirectWithId('formCalificaciones', 'califBase')">
                        <div class="mb-2">
                            <select id="asignacionCalificaciones" class="form-select form-select-sm" required>
                                <option value="">Seleccione asignación...</option>
                                @foreach($asignaciones as $a)
                                <option value="{{ $a->id_asignacion }}">
                                    [{{ $a->codigo ?? '' }}] {{ $a->materia->nombre ?? '—' }} - {{ $a->curso->nombre ?? '—' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-pdf"><i class="bi bi-file-earmark-pdf me-1"></i> Generar PDF</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Asistencia PDF --}}
        <div class="col-md-4">
            <div class="card report-card">
                <div class="card-header"><i class="bi bi-file-earmark-pdf me-2"></i>Asistencia</div>
                <div class="card-body">
                    <p><i class="bi bi-calendar-check me-1 text-muted"></i> Reporte PDF de asistencia por asignación, con registro por fecha y porcentaje de asistencia.</p>
                    <form id="formAsistencia" onsubmit="return redirectWithId('formAsistencia', 'asistBase')">
                        <div class="mb-2">
                            <select id="asignacionAsistencia" class="form-select form-select-sm" required>
                                <option value="">Seleccione asignación...</option>
                                @foreach($asignaciones as $a)
                                <option value="{{ $a->id_asignacion }}">
                                    [{{ $a->codigo ?? '' }}] {{ $a->materia->nombre ?? '—' }} - {{ $a->curso->nombre ?? '—' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-pdf"><i class="bi bi-file-earmark-pdf me-1"></i> Generar PDF</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Horario PDF --}}
        <div class="col-md-4">
            <div class="card report-card">
                <div class="card-header"><i class="bi bi-file-earmark-pdf me-2"></i>Horario por Curso</div>
                <div class="card-body">
                    <p><i class="bi bi-calendar-week me-1 text-muted"></i> Reporte PDF del horario semanal de un curso, con materias, docentes y horarios.</p>
                    <form id="formHorario" onsubmit="return redirectWithId('formHorario', 'horarioBase')">
                        <div class="mb-2">
                            <select id="cursoHorario" class="form-select form-select-sm" required>
                                <option value="">Seleccione curso...</option>
                                @foreach($cursos as $c)
                                <option value="{{ $c->id_curso }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-pdf"><i class="bi bi-file-earmark-pdf me-1"></i> Generar PDF</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Horario Web --}}
        <div class="col-md-4">
            <div class="card report-card">
                <div class="card-header"><i class="bi bi-eye me-2"></i>Horario Web</div>
                <div class="card-body">
                    <p><i class="bi bi-laptop me-1 text-muted"></i> Visualice el horario semanal de un curso directamente en el navegador.</p>
                    <form method="GET" action="{{ route('admin.reportes.index') }}">
                        <div class="mb-2">
                            <select name="curso_horario" class="form-select form-select-sm">
                                <option value="">Seleccione curso...</option>
                                @foreach($cursos as $c)
                                <option value="{{ $c->id_curso }}" {{ request('curso_horario') == $c->id_curso ? 'selected' : '' }}>{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-pdf"><i class="bi bi-search me-1"></i> Ver Horario</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Detalle General --}}
        <div class="col-md-4">
            <div class="card report-card">
                <div class="card-header"><i class="bi bi-info-circle me-2"></i>Resumen General</div>
                <div class="card-body">
                    <p><i class="bi bi-pie-chart me-1 text-muted"></i> Tablas detalladas de asignaciones por curso y por docente en la sección inferior.</p>
                    <div class="d-flex gap-2">
                        <span class="badge rounded-pill" style="background:#1e3c72;">{{ $totalCursos }} cursos</span>
                        <span class="badge rounded-pill" style="background:#2e7d32;">{{ $totalDocentes }} docentes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Horario web table --}}
    @if($horarioPorCurso)
    @php $diasOrd = ['lunes','martes','miercoles','jueves','viernes','sabado']; $diasShort = ['LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB']; $franjas = ['14:00','14:40','15:20','16:30','17:10']; @endphp
    <div class="card table-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-week me-2"></i>Horario: {{ $cursos->firstWhere('id_curso', request('curso_horario'))->nombre ?? '' }}</span>
            <span class="badge bg-light text-dark">Lunes a Sábado · 14:00 - 17:50</span>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="horario-table table mb-0">
                    <thead>
                        <tr>
                            <th>Hora</th>
                            @foreach($diasShort as $d)
                            <th>{{ $d }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($franjas as $idx => $franja)
                        <tr>
                            <td class="hora-col">{{ $franja }}</td>
                            @foreach($diasOrd as $d)
                            <td>
                                @php
                                    $clases = collect($horarioPorCurso->get($d, []))->filter(function($h) use ($franja) {
                                        return substr($h->hora_inicio, 0, 5) === $franja;
                                    });
                                @endphp
                                @forelse($clases as $c)
                                <div class="horario-badge">
                                    <div class="codigo">{{ $c->asignacion->codigo }}</div>
                                    <div class="materia">{{ $c->asignacion->materia->nombre ?? '' }}</div>
                                    <div class="docente">{{ $c->asignacion->docente->usuario->nombre ?? '' }}</div>
                                </div>
                                @empty
                                <span class="text-muted" style="font-size:0.7rem;">—</span>
                                @endforelse
                            </td>
                            @endforeach
                        </tr>
                        @if($idx === 2)
                        <tr class="receso-row">
                            <td class="hora-col">16:00</td>
                            <td colspan="6"><i class="bi bi-cup-hot me-1"></i> RECREO · 16:00 - 16:30</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i> Cada período dura 40 minutos. Recreo de 16:00 a 16:30.</p>
        </div>
    </div>
    @endif

    {{-- Asignaciones por Curso --}}
    <div class="card table-card mb-4">
        <div class="card-header"><i class="bi bi-diagram-3 me-2"></i>Asignaciones por Curso</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Materias</th>
                            <th>Docentes</th>
                            <th style="width:80px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignacionesPorCurso as $item)
                        <tr>
                            <td class="fw-bold" style="color:#1e3c72;">{{ $item['curso'] }}</td>
                            <td><span class="text-muted small">{{ $item['materias'] }}</span></td>
                            <td><span class="text-muted small">{{ $item['docentes'] }}</span></td>
                            <td><span class="badge rounded-pill" style="background:#1e3c72;">{{ $item['total'] }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-muted text-center py-3">No hay asignaciones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Asignaciones por Docente --}}
    <div class="card table-card">
        <div class="card-header"><i class="bi bi-person-badge me-2"></i>Asignaciones por Docente</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Docente</th>
                            <th>Cursos</th>
                            <th style="width:80px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignacionesPorDocente as $item)
                        <tr>
                            <td class="fw-bold" style="color:#1e3c72;">{{ $item['docente'] }}</td>
                            <td><span class="text-muted small">{{ $item['cursos'] }}</span></td>
                            <td><span class="badge rounded-pill" style="background:#2e7d32;">{{ $item['total'] }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-muted text-center py-3">No hay asignaciones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var califBase = '{{ route("admin.reportes.calificaciones", ["asignacion" => "PLACEHOLDER"]) }}';
var asistBase = '{{ route("admin.reportes.asistencia", ["asignacion" => "PLACEHOLDER"]) }}';
var horarioBase = '{{ route("admin.reportes.horario", ["curso" => "PLACEHOLDER"]) }}';

function redirectWithId(formId, baseVar) {
    var selectId;
    if (formId === 'formCalificaciones') selectId = 'asignacionCalificaciones';
    else if (formId === 'formAsistencia') selectId = 'asignacionAsistencia';
    else if (formId === 'formHorario') selectId = 'cursoHorario';

    var id = document.getElementById(selectId).value;
    if (!id) { alert('Seleccione una opci\u00f3n'); return false; }

    var base = window[baseVar];
    window.open(base.replace('PLACEHOLDER', id), '_blank');
    return false;
}
</script>
@endpush
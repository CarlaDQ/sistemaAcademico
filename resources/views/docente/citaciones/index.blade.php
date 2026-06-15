@extends('layouts.app')

@section('title', 'Citaciones - Docente')

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
        background: linear-gradient(135deg, #1a73e8, #1557b0);
        color: #fff;
        border-radius: 12px 12px 0 0;
        padding: 16px 24px;
        font-weight: 600;
    }
    .btn-primary-custom {
        background: #1a73e8;
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-primary-custom:hover { background: #1557b0; transform: translateY(-1px); }
    .btn-primary-custom i { margin-right: 6px; }
    .table th {
        background: #f8f9fa;
        border-bottom: 2px solid #1a73e8;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #555;
    }
    .table td { vertical-align: middle; }
    .table tr:nth-child(even) { background: #f8faff; }
    .table tr:hover { background: #e3f2fd; }
    .badge-citacion { background: #e3f2fd; color: #1565c0; }
    .badge-aviso { background: #fff3e0; color: #e65100; }
    .badge-comunicado { background: #e8f5e9; color: #2e7d32; }
    .badge-pendiente { background: #fff3e0; color: #e65100; }
    .badge-enviada { background: #e3f2fd; color: #1565c0; }
    .badge-leida { background: #e8f5e9; color: #2e7d32; }
    .badge-respondida { background: #f3e5f5; color: #7b1fa2; }
    .search-box { position: relative; }
    .search-box .bi-search { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #999; }
    .search-box input { padding-left: 36px; border-radius: 8px; border: 1px solid #ddd; }
    .search-box input:focus { border-color: #1a73e8; box-shadow: 0 0 0 2px rgba(26,115,232,0.15); outline: none; }
    .modal-header-custom {
        background: linear-gradient(135deg, #1a73e8, #1557b0);
        color: #fff;
        border-radius: 12px 12px 0 0;
    }
    .modal-header-custom .btn-close { filter: brightness(0) invert(1); }
    .modal-content { border-radius: 12px; border: none; }
    .form-control:focus, .form-select:focus {
        border-color: #1a73e8;
        box-shadow: 0 0 0 2px rgba(26,115,232,0.15);
    }
    .stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 16px 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        border-left: 4px solid #1a73e8;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .number { font-size: 1.5rem; font-weight: 700; color: #1a73e8; }
    .stat-card .label { font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
    #studentSelect optgroup label { font-weight: 600; color: #1a73e8; }
    #studentSelect option { padding: 4px 8px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1a73e8;"><i class="bi bi-envelope-paper me-2"></i>Citaciones, Avisos y Comunicados</h3>
            <p class="text-muted small mb-0">Gestione las comunicaciones con los padres de familia</p>
        </div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg"></i> Nueva Citaci&oacute;n
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="label">Citaciones</div>
                <div class="number">{{ $citaciones->where('tipo', 'citacion')->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #e65100;">
                <div class="label">Avisos</div>
                <div class="number" style="color: #e65100;">{{ $citaciones->where('tipo', 'aviso')->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #2e7d32;">
                <div class="label">Comunicados</div>
                <div class="number" style="color: #2e7d32;">{{ $citaciones->where('tipo', 'comunicado')->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #7b1fa2;">
                <div class="label">Pendientes</div>
                <div class="number" style="color: #7b1fa2;">{{ $citaciones->where('estado', 'pendiente')->count() }}</div>
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list me-2"></i>Historial de Comunicaciones</span>
            <span class="badge bg-light text-dark">{{ $citaciones->count() }} registros</span>
        </div>
        <div class="card-body">
            @include('partials.flash-messages')

            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <select name="id_curso" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Todos los cursos</option>
                        @foreach($cursos as $c)
                        <option value="{{ $c->id_curso }}" {{ request('id_curso') == $c->id_curso ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tipo" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Todos los tipos</option>
                        <option value="citacion" {{ request('tipo') == 'citacion' ? 'selected' : '' }}>Citaci&oacute;n</option>
                        <option value="aviso" {{ request('tipo') == 'aviso' ? 'selected' : '' }}>Aviso</option>
                        <option value="comunicado" {{ request('tipo') == 'comunicado' ? 'selected' : '' }}>Comunicado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    @if(request('id_curso') || request('tipo'))
                    <a href="{{ route('docente.citaciones.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar filtros</a>
                    @endif
                </div>
            </form>

            @if($citaciones->isEmpty() && !request('id_curso') && !request('tipo'))
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-envelope-open" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="mt-3">No hay citaciones registradas.</p>
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="bi bi-plus-lg"></i> Crear primera comunicaci&oacute;n
                    </button>
                </div>
            @elseif($citaciones->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-envelope-open" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="mt-3">No se encontraron comunicaciones con los filtros actuales.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" id="citacionesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Estudiante</th>
                                <th>Curso</th>
                                <th>T&iacute;tulo</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha/Hora</th>
                                <th>Creaci&oacute;n</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($citaciones as $idx => $c)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <strong>{{ optional($c->estudiante->usuario)->nombre ?? 'Desconocido' }} {{ optional($c->estudiante->usuario)->apellido ?? '' }}</strong>
                                    </td>
                                    <td><small class="text-muted">{{ optional($c->estudiante->curso)->nombre ?? '-' }}</small></td>
                                    <td>{{ $c->titulo }}</td>
                                    <td>
                                        <span class="badge rounded-pill
                                            {{ $c->tipo == 'citacion' ? 'badge-citacion' : ($c->tipo == 'aviso' ? 'badge-aviso' : 'badge-comunicado') }}">
                                            {{ ucfirst($c->tipo) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill
                                            {{ $c->estado == 'pendiente' ? 'badge-pendiente' : ($c->estado == 'enviada' ? 'badge-enviada' : ($c->estado == 'leida' ? 'badge-leida' : 'badge-respondida')) }}">
                                            {{ ucfirst($c->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($c->fecha_citacion)
                                            {{ $c->fecha_citacion->format('d/m/Y') }}
                                            @if($c->hora_citacion) {{ date('H:i', strtotime($c->hora_citacion)) }} @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $c->created_at ? $c->created_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nueva Comunicaci&oacute;n</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('docente.citaciones.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Curso <span class="text-danger">*</span></label>
                            <select name="id_curso" id="modalCurso" class="form-select" required>
                                <option value="">Seleccionar curso...</option>
                                @foreach($cursos as $c)
                                <option value="{{ $c->id_curso }}">{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select" required>
                                <option value="citacion">Citaci&oacute;n</option>
                                <option value="aviso">Aviso</option>
                                <option value="comunicado" selected>Comunicado</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="estudianteField" style="display:none;">
                            <label class="form-label fw-semibold">Estudiante (opcional)</label>
                            <div class="d-flex gap-2">
                                <select name="id_estudiante" id="modalEstudiante" class="form-select">
                                    <option value="">Todos los estudiantes del curso</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cargarEstudiantes()" title="Actualizar lista">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <small class="text-muted">Si no selecciona un estudiante espec&iacute;fico, se enviar&aacute; a todo el curso.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">T&iacute;tulo <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" class="form-control" required maxlength="255" placeholder="T&iacute;tulo de la comunicaci&oacute;n">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mensaje <span class="text-danger">*</span></label>
                            <textarea name="mensaje" class="form-control" rows="4" required placeholder="Describa el motivo..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="fecha_citacion" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hora</label>
                            <input type="time" name="hora_citacion" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lugar</label>
                            <input type="text" name="lugar" class="form-control" placeholder="Ej: Sala de reuniones">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-send me-1"></i> Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('modalCurso').addEventListener('change', function() {
        var cursoId = this.value;
        var field = document.getElementById('estudianteField');
        var select = document.getElementById('modalEstudiante');
        if (cursoId) {
            field.style.display = 'block';
            select.innerHTML = '<option value="">Cargando estudiantes...</option>';
            fetch('{{ url("docente/citaciones/estudiantes") }}/' + cursoId)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    select.innerHTML = '<option value="">Todos los estudiantes del curso</option>';
                    data.forEach(function(e) {
                        var opt = document.createElement('option');
                        opt.value = e.id;
                        opt.textContent = e.nombre;
                        select.appendChild(opt);
                    });
                })
                .catch(function() {
                    select.innerHTML = '<option value="">Error al cargar estudiantes</option>';
                });
        } else {
            field.style.display = 'none';
            select.innerHTML = '<option value="">Todos los estudiantes del curso</option>';
        }
    });

    function cargarEstudiantes() {
        var cursoId = document.getElementById('modalCurso').value;
        if (cursoId) {
            document.getElementById('modalCurso').dispatchEvent(new Event('change'));
        }
    }
</script>
@endpush
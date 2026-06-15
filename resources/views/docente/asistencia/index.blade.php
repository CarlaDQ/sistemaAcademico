@extends('layouts.app')

@section('title', 'Asistencia - Docente')

@push('styles')
<style>
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .card-custom {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: none;
    }
    .card-custom .card-header {
        background: linear-gradient(135deg, #1a73e8, #1557b0);
        color: #fff;
        border-radius: 12px 12px 0 0;
        padding: 16px 24px;
        font-weight: 600;
    }
    .table th {
        background: #f8f9fa;
        border-bottom: 2px solid #1a73e8;
    }
    .table td { vertical-align: middle; }
    .table tr:nth-child(even) { background: #f8faff; }
    .table tr:hover { background: #e3f2fd; }
    .btn-radio-group .btn {
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.85rem;
        margin: 0 2px;
    }
    .btn-radio-group .btn-check:checked + .btn-outline-success { background: #28a745; color: #fff; border-color: #28a745; }
    .btn-radio-group .btn-check:checked + .btn-outline-danger { background: #dc3545; color: #fff; border-color: #dc3545; }
    .btn-radio-group .btn-check:checked + .btn-outline-warning { background: #ffc107; color: #000; border-color: #ffc107; }
    .btn-radio-group .btn-check:checked + .btn-outline-info { background: #17a2b8; color: #fff; border-color: #17a2b8; }
    .btn-radio-group .btn-check:checked + .btn-outline-secondary { background: #6c757d; color: #fff; border-color: #6c757d; }
    .btn-submit {
        background: #1a73e8;
        color: #fff;
        border: none;
        padding: 10px 36px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-submit:hover { background: #1557b0; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26,115,232,0.3); }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="card card-custom">
        <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Control de Asistencia</h4>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4 align-items-end" id="filterForm">
                <div class="col-md-4">
                    <label class="form-label text-muted small">Asignaci&oacute;n</label>
                    <select name="asignacion" class="form-select" onchange="this.form.submit()">
                        <option value="">Seleccionar...</option>
                        @foreach($asignaciones as $a)
                            <option value="{{ $a->id_asignacion }}" {{ $a->id_asignacion == $asignacionId ? 'selected' : '' }}>
                                {{ $a->materia->nombre }} - {{ $a->curso->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ $fecha }}" onchange="this.form.submit()">
                </div>
            </form>

            @include('partials.flash-messages')

            @if($estudiantes->isNotEmpty())
                <form method="POST" action="{{ route('docente.asistencia.store') }}">
                    @csrf
                    <input type="hidden" name="id_asignacion" value="{{ $asignacionId }}">
                    <input type="hidden" name="fecha" value="{{ $fecha }}">

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Estudiante</th>
                                    <th style="width:400px;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estudiantes as $idx => $est)
                                    @php
                                        $asis = $asistenciasExistentes->get($est->id_estudiante);
                                        $estadoActual = $asis ? $asis->estado : '';
                                    @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <strong>{{ $est->usuario->nombre }} {{ $est->usuario->apellido }}</strong>
                                            @if($estadoActual == 'permiso' && $asis && $asis->observacion)
                                                <br><small class="text-muted" style="font-size:0.75rem;"><i class="bi bi-chat-quote me-1"></i>{{ $asis->observacion }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-radio-group" role="group">
                                                <input type="radio" class="btn-check" name="asistencia[{{ $est->id_estudiante }}]"
                                                    id="presente_{{ $est->id_estudiante }}" value="presente"
                                                    {{ $estadoActual == 'presente' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success btn-sm" for="presente_{{ $est->id_estudiante }}">
                                                    <i class="bi bi-check-circle"></i> Presente
                                                </label>

                                                <input type="radio" class="btn-check" name="asistencia[{{ $est->id_estudiante }}]"
                                                    id="ausente_{{ $est->id_estudiante }}" value="ausente"
                                                    {{ $estadoActual == 'ausente' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger btn-sm" for="ausente_{{ $est->id_estudiante }}">
                                                    <i class="bi bi-x-circle"></i> Ausente
                                                </label>

                                                <input type="radio" class="btn-check" name="asistencia[{{ $est->id_estudiante }}]"
                                                    id="tardanza_{{ $est->id_estudiante }}" value="tardanza"
                                                    {{ $estadoActual == 'tardanza' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning btn-sm" for="tardanza_{{ $est->id_estudiante }}">
                                                    <i class="bi bi-clock"></i> Tardanza
                                                </label>

                                                <input type="radio" class="btn-check" name="asistencia[{{ $est->id_estudiante }}]"
                                                    id="justificado_{{ $est->id_estudiante }}" value="justificado"
                                                    {{ $estadoActual == 'justificado' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-info btn-sm" for="justificado_{{ $est->id_estudiante }}">
                                                    <i class="bi bi-file-text"></i> Justificado
                                                </label>

                                                <input type="radio" class="btn-check" name="asistencia[{{ $est->id_estudiante }}]"
                                                    id="permiso_{{ $est->id_estudiante }}" value="permiso"
                                                    {{ $estadoActual == 'permiso' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-secondary btn-sm" for="permiso_{{ $est->id_estudiante }}">
                                                    <i class="bi bi-file-earmark-text"></i> Permiso
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save me-1"></i> Guardar Asistencia
                        </button>
                    </div>
                </form>
            @elseif($asignacionId)
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                    <p class="mt-2">No hay estudiantes en esta asignaci&oacute;n.</p>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
                    <p class="mt-2">Seleccione una asignaci&oacute;n y fecha para comenzar.</p>
                </div>
            @endif
        </div>
    </div>
    
</div>
@endsection
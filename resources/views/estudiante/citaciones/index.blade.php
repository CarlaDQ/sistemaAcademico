@extends('layouts.app')

@section('title', 'Mis Citaciones - Estudiante')

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
    .badge-citacion { background: #e3f2fd; color: #1565c0; }
    .badge-aviso { background: #fff3e0; color: #e65100; }
    .badge-comunicado { background: #e8f5e9; color: #2e7d32; }
    .citacion-card {
        border-left: 4px solid #1e3c72;
        padding: 16px;
        margin-bottom: 16px;
        background: #f8f9fa;
        border-radius: 0 8px 8px 0;
        transition: all 0.2s;
    }
    .citacion-card:hover { background: #e3f2fd; }
    .citacion-card .fecha { color: #999; font-size: 0.85rem; }
    .citacion-card .mensaje { margin-top: 8px; white-space: pre-wrap; }
    .btn-read {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        color: #fff;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        font-size: 0.85rem;
    }
    .btn-read:hover { background: #152a4f; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="card card-custom">
        <div class="card-header">
            <h4 class="mb-0"><i class="bi bi-envelope-paper me-2"></i>Mis Citaciones y Notificaciones</h4>
        </div>
        <div class="card-body">
            @if($citaciones->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">No tienes citaciones pendientes.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>T&iacute;tulo</th>
                                <th>Docente</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha/Hora</th>
                                <th>Lugar</th>
                                <th>Acci&oacute;n</th>
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
                                            {{ $c->estado == 'pendiente' ? 'bg-warning text-dark' : ($c->estado == 'leida' ? 'bg-success' : 'bg-secondary') }}">
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
                                    <td>{{ $c->lugar ?? '-' }}</td>
                                    <td>
                                        <button class="btn-read btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $c->id_citacion }}">
                                            <i class="bi bi-eye"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($citaciones as $c)
    <div class="modal fade" id="viewModal{{ $c->id_citacion }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e3c72, #2a4a7f); color: #fff;">
                    <h5 class="modal-title">{{ $c->titulo }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-1">
                        <strong>De:</strong> {{ $c->docente->usuario->nombre ?? '' }} {{ $c->docente->usuario->apellido ?? '' }}
                    </p>
                    <p class="text-muted mb-1">
                        <strong>Tipo:</strong> {{ ucfirst($c->tipo) }}
                    </p>
                    @if($c->fecha_citacion)
                        <p class="text-muted mb-1">
                            <strong>Fecha:</strong> {{ $c->fecha_citacion->format('d/m/Y') }}
                            @if($c->hora_citacion) <strong>Hora:</strong> {{ date('H:i', strtotime($c->hora_citacion)) }} @endif
                        </p>
                    @endif
                    <hr>
                    <p style="white-space: pre-wrap;">{{ $c->mensaje }}</p>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
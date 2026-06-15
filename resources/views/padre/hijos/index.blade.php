@extends('layouts.app')

@section('title', 'Mis Hijos - Padre')

@push('styles')
<style>
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .child-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
    }
    .child-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
    .child-card .card-img-top {
        height: 120px;
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .child-card .avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #fff;
        font-weight: 700;
        border: 3px solid rgba(255,255,255,0.4);
    }
    .child-card .card-body { padding: 20px; }
    .child-card .card-title { font-weight: 700; font-size: 1.15rem; }
    .stat-item {
        padding: 12px;
        border-radius: 10px;
        text-align: center;
    }
    .stat-item .number { font-size: 1.5rem; font-weight: 700; }
    .stat-item .label { font-size: 0.8rem; color: #666; }
    .stat-grade { background: #e3f2fd; }
    .stat-attendance { background: #e8f5e9; }
    .stat-grade .number { color: #1e3c72; }
    .stat-attendance .number { color: #28a745; }
    .btn-view {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        color: #fff;
        border: none;
        padding: 8px 24px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }
    .btn-view:hover { background: #152a4f; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 style="color:#1e3c72;font-weight:700;"><i class="bi bi-people-fill me-2"></i>Mis Hijos</h3>
    </div>

    @include('partials.flash-messages')

    @if($hijos->isEmpty())
        <div class="text-center py-5">
            <div class="card-custom d-inline-block p-5" style="background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                <i class="bi bi-emoji-frown" style="font-size: 3rem; color: #ccc;"></i>
                <p class="mt-3 text-muted">No hay hijos registrados.</p>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($hijos as $hijo)
                <div class="col-md-6 col-lg-4">
                    <div class="child-card">
                        <div class="card-img-top">
                            <div class="avatar">
                                {{ substr($hijo->usuario->nombre ?? '?', 0, 1) }}{{ substr($hijo->usuario->apellido ?? '', 0, 1) }}
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-center">
                                {{ $hijo->usuario->nombre ?? '' }} {{ $hijo->usuario->apellido ?? '' }}
                            </h5>
                            <p class="text-center text-muted small mb-3">
                                <i class="bi bi-mortarboard"></i> {{ $hijo->curso->nombre ?? 'Sin curso' }}
                            </p>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="stat-item stat-grade">
                                        <div class="number">{{ $hijo->promedio }}</div>
                                        <div class="label">Promedio</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item stat-attendance">
                                        <div class="number">{{ $hijo->asistencia_pct }}%</div>
                                        <div class="label">Asistencia</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <a href="{{ route('padre.hijos.show', $hijo->id_estudiante) }}" class="btn-view">
                                    <i class="bi bi-eye me-1"></i> Ver Detalle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
@extends('layouts.app')

@section('title', 'Reportes - Docente')

@push('styles')
<style>
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .card-custom {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: none;
        margin-bottom: 24px;
    }
    .card-custom .card-header {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        color: #fff;
        border-radius: 14px 14px 0 0;
        padding: 16px 24px;
        font-weight: 600;
        border: none;
    }
    .report-card {
        background: #fff;
        border: 1.5px solid #e8ecf0;
        border-radius: 14px;
        padding: 28px;
        transition: all 0.3s;
        cursor: pointer;
        text-decoration: none;
        display: block;
        color: inherit;
        height: 100%;
    }
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(30,60,114,0.12);
        border-color: #1e3c72;
    }
    .report-card .icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 16px;
    }
    .report-card .title { font-weight: 700; font-size: 1.15rem; margin-bottom: 6px; color: #1e3c72; }
    .report-card .desc { font-size: 0.85rem; color: #888; line-height: 1.5; }
    .report-card .action-hint { font-size: 0.8rem; font-weight: 600; margin-top: 14px; }
    .form-control:focus, .form-select:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 2px rgba(30,60,114,0.15);
    }
    .btn-generate {
        background: linear-gradient(135deg, #1e3c72, #2a4a7f);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 28px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-generate:hover { background: linear-gradient(135deg, #152a4f, #1e3c72); color: #fff; transform: translateY(-1px); }
    .form-label { font-size: 0.85rem; color: #444; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1e3c72;"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reportes Académicos</h3>
            <p class="text-muted small mb-0">Genere reportes detallados de rendimiento por estudiante o por curso</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <a href="javascript:void(0)" class="report-card" onclick="document.getElementById('reportType').value='estudiante'; document.querySelector('[name=asignacion]').focus(); document.querySelector('[name=asignacion]').scrollIntoView({behavior:'smooth'});">
                <div class="icon" style="background: #e3f2fd; color: #1e3c72;">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="title">Reporte por Estudiante</div>
                <div class="desc">Calificaciones detalladas por período, promedio general y citaciones de un estudiante en particular. Seleccione la asignación y el estudiante para generar el reporte.</div>
                <div class="action-hint" style="color:#1e3c72;">Seleccionar y generar &rarr;</div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="javascript:void(0)" class="report-card" onclick="document.getElementById('reportType').value='curso'; document.querySelector('[name=asignacion]').focus(); document.querySelector('[name=asignacion]').scrollIntoView({behavior:'smooth'});">
                <div class="icon" style="background: #e8f5e9; color: #2e7d32;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="title">Reporte por Curso</div>
                <div class="desc">Vista general del rendimiento de todo un curso. Promedios, cantidad de aprobados y reprobados, y detalle por estudiante con acceso a su reporte individual.</div>
                <div class="action-hint" style="color:#2e7d32;">Seleccionar y generar &rarr;</div>
            </a>
        </div>
    </div>

    <div class="card card-custom mt-4">
        <div class="card-header">
            <i class="bi bi-funnel me-2"></i>Seleccionar Parámetros del Reporte
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold"><i class="bi bi-book me-1"></i> Asignación</label>
                    <select name="asignacion" class="form-select" required>
                        <option value="">Seleccionar asignación...</option>
                        @foreach($asignaciones as $a)
                            <option value="{{ $a->id_asignacion }}">[{{ $a->codigo ?? '' }}] {{ optional($a->materia)->nombre ?? 'N/A' }} - {{ optional($a->curso)->nombre ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><i class="bi bi-file-text me-1"></i> Tipo de Reporte</label>
                    <select id="reportType" class="form-select" required>
                        <option value="">Seleccionar tipo...</option>
                        <option value="estudiante">Por Estudiante</option>
                        <option value="curso">Por Curso</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-generate w-100" onclick="generateReport()">
                        <i class="bi bi-search me-1"></i> Generar Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport() {
    const asignacion = document.querySelector('[name="asignacion"]').value;
    const type = document.getElementById('reportType').value;
    if (!asignacion || !type) {
        alert('Seleccione asignación y tipo de reporte para continuar.');
        return;
    }

    if (type === 'estudiante') {
        window.location.href = '{{ url("docente/reportes/estudiante") }}/0?asignacion=' + asignacion;
    } else {
        window.location.href = '{{ url("docente/reportes/curso") }}/0?asignacion=' + asignacion;
    }
}
</script>
@endsection
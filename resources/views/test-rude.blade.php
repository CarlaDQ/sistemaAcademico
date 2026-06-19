@extends('layouts.app')

@section('title', 'Prueba Integración RUDE + SEGIP')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-grid-3x3-gap me-2"></i>Prueba de Integración RUDE + SEGIP</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>API Simulada:</strong> <code>http://localhost/APISIMULADO</code>
            </div>
            <h5 class="mb-3">Resultados:</h5>
            <ul class="list-group">
                @foreach($resultados as $r)
                    <li class="list-group-item">{{ $r }}</li>
                @endforeach
            </ul>
            <hr>
            <p class="text-muted small">
                <strong>Flujo completo:</strong> Poblar SEGIP → Consultar por CI → Registrar en RUDE desde datos del SEGIP
            </p>
            <a href="{{ url('/test-rude') }}" class="btn btn-primary">
                <i class="bi bi-arrow-clockwise me-1"></i> Repetir Prueba
            </a>
            <a href="http://localhost/APISIMULADO/" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-box-arrow-up-right me-1"></i> Abrir API
            </a>
        </div>
    </div>
</div>
@endsection

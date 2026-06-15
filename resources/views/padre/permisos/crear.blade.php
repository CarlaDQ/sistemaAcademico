@extends('layouts.app')

@section('title', 'Solicitar Permiso - ' . $hijo->usuario->nombre . ' ' . $hijo->usuario->apellido)

@push('styles')
<style>
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
    .permiso-header { background: linear-gradient(135deg, #f57c00, #ff9800); border-radius: 16px; padding: 24px 32px; color: #fff; margin-bottom: 24px; box-shadow: 0 4px 16px rgba(245,124,0,0.3); }
    .card-custom { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: none; }
    .card-custom .card-header { background: #f8f9fa; border-radius: 12px 12px 0 0; padding: 14px 20px; font-weight: 600; border-bottom: 2px solid #f57c00; }
    .form-label { font-weight: 500; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="permiso-header d-flex align-items-center gap-3">
        <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
        <div>
            <h4 class="mb-0">Solicitar Permiso</h4>
            <p class="mb-0 opacity-75">{{ $hijo->usuario->nombre }} {{ $hijo->usuario->apellido }} &middot; {{ $hijo->curso->nombre ?? 'Sin curso' }}</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header">
                    <i class="bi bi-pencil-square me-2"></i>Detalles del Permiso
                </div>
                <div class="card-body">
                    <form action="{{ route('padre.permiso.guardar') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_estudiante" value="{{ $hijo->id_estudiante }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Materia <span class="text-danger">*</span></label>
                                <select name="id_asignacion" class="form-select @error('id_asignacion') is-invalid @enderror" required>
                                    <option value="">Seleccione la materia...</option>
                                    @foreach($asignaciones as $a)
                                        <option value="{{ $a->id_asignacion }}" {{ old('id_asignacion') == $a->id_asignacion ? 'selected' : '' }}>
                                            {{ $a->materia->nombre ?? 'Materia' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_asignacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha del permiso <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                                    value="{{ old('fecha', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Motivo del permiso <span class="text-danger">*</span></label>
                                <textarea name="motivo" class="form-control @error('motivo') is-invalid @enderror"
                                    rows="4" placeholder="Describa el motivo del permiso..." required>{{ old('motivo') }}</textarea>
                                @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning"><i class="bi bi-send me-1"></i> Solicitar Permiso</button>
                            <a href="{{ route('padre.hijos.show', $hijo->id_estudiante) }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

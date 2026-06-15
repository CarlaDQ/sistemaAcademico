@extends('layouts.app')

@section('title', isset($docente) ? 'Editar Docente' : 'Nuevo Docente')

@section('content')
<div class="page-header mb-4">
    <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">{{ isset($docente) ? 'Editar Docente' : 'Nuevo Docente' }}</h3>
    <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Complete los campos del formulario</p>
</div>
<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:16px 24px;font-weight:600;border:none;font-size:1rem;">{{ isset($docente) ? 'Editar Docente' : 'Nuevo Docente' }}</div>
    <div class="card-body" style="padding:24px;">
        <form action="{{ isset($docente) ? route('admin.docentes.update', $docente->id_docente) : route('admin.docentes.store') }}" method="POST">
            @csrf
            @if(isset($docente)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $docente->usuario->nombre ?? '') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                           value="{{ old('apellido', $docente->usuario->apellido ?? '') }}" required>
                    @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Teléfono</label>
                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono', $docente->usuario->telefono ?? '') }}">
                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Código (autogenerado)</label>
                    <input type="text" class="form-control" value="{{ $docente->codigo_docente ?? 'DOC + ID' }}" readonly disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Especialidad</label>
                    <input type="text" name="especialidad" class="form-control @error('especialidad') is-invalid @enderror"
                           value="{{ old('especialidad', $docente->especialidad ?? '') }}">
                    @error('especialidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Título Académico</label>
                    <input type="text" name="titulo_academico" class="form-control @error('titulo_academico') is-invalid @enderror"
                           value="{{ old('titulo_academico', $docente->titulo_academico ?? '') }}">
                    @error('titulo_academico') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Fecha de Ingreso</label>
                    <input type="date" name="fecha_ingreso" class="form-control @error('fecha_ingreso') is-invalid @enderror"
                           value="{{ old('fecha_ingreso', isset($docente) && $docente->fecha_ingreso ? $docente->fecha_ingreso->format('Y-m-d') : '') }}">
                    @error('fecha_ingreso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @if(!isset($docente))
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Confirmar Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                @else
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nueva Contraseña (dejar en blanco para mantener)</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                @endif
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 24px;font-weight:600;"><i class="bi bi-save me-1"></i> {{ isset($docente) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.docentes.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:8px 24px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

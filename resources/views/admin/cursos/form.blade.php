@extends('layouts.app')

@section('title', isset($curso) ? 'Editar Curso' : 'Nuevo Curso')

@section('content')
<div class="page-header mb-4">
    <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">{{ isset($curso) ? 'Editar Curso' : 'Nuevo Curso' }}</h3>
    <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Complete los campos del formulario</p>
</div>
<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:16px 24px;font-weight:600;border:none;font-size:1rem;">{{ isset($curso) ? 'Editar Curso' : 'Nuevo Curso' }}</div>
    <div class="card-body" style="padding:24px;">
        <form action="{{ isset($curso) ? route('admin.cursos.update', $curso->id_curso) : route('admin.cursos.store') }}" method="POST">
            @csrf
            @if(isset($curso)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $curso->nombre ?? '') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nivel Educativo <span class="text-danger">*</span></label>
                    <select name="id_nivel" class="form-select @error('id_nivel') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($niveles as $n)
                        <option value="{{ $n->id_nivel }}" {{ old('id_nivel', $curso->id_nivel ?? '') == $n->id_nivel ? 'selected' : '' }}>
                            {{ $n->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_nivel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Año Lectivo <span class="text-danger">*</span></label>
                    <select name="id_anio" class="form-select @error('id_anio') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($anios as $a)
                        <option value="{{ $a->id_anio }}" {{ old('id_anio', $curso->id_anio ?? '') == $a->id_anio ? 'selected' : '' }}>
                            {{ $a->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_anio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Grado</label>
                    <input type="text" name="grado" class="form-control @error('grado') is-invalid @enderror"
                           value="{{ old('grado', $curso->grado ?? '') }}">
                    @error('grado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Sección</label>
                    <input type="text" name="seccion" class="form-control @error('seccion') is-invalid @enderror"
                           value="{{ old('seccion', $curso->seccion ?? '') }}" maxlength="10">
                    @error('seccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Capacidad</label>
                    <input type="number" name="capacidad" class="form-control @error('capacidad') is-invalid @enderror"
                           value="{{ old('capacidad', $curso->capacidad ?? '') }}" min="1" max="100">
                    @error('capacidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 24px;font-weight:600;"><i class="bi bi-save me-1"></i> {{ isset($curso) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.cursos.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:8px 24px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

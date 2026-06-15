@extends('layouts.app')

@section('title', isset($materia) ? 'Editar Materia' : 'Nueva Materia')

@section('content')
<div class="page-header mb-4">
    <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">{{ isset($materia) ? 'Editar Materia' : 'Nueva Materia' }}</h3>
    <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Complete los campos del formulario</p>
</div>
<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:16px 24px;font-weight:600;border:none;font-size:1rem;">{{ isset($materia) ? 'Editar Materia' : 'Nueva Materia' }}</div>
    <div class="card-body" style="padding:24px;">
        <form action="{{ isset($materia) ? route('admin.materias.update', $materia->id_materia) : route('admin.materias.store') }}" method="POST">
            @csrf
            @if(isset($materia)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $materia->nombre ?? '') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Código <span class="text-danger">*</span></label>
                    <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
                           value="{{ old('codigo', $materia->codigo ?? '') }}" required maxlength="20">
                    @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nivel Educativo <span class="text-danger">*</span></label>
                    <select name="id_nivel" class="form-select @error('id_nivel') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($niveles as $n)
                        <option value="{{ $n->id_nivel }}" {{ old('id_nivel', $materia->id_nivel ?? '') == $n->id_nivel ? 'selected' : '' }}>
                            {{ $n->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_nivel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Horas Semanales</label>
                    <input type="number" name="horas_semanales" class="form-control @error('horas_semanales') is-invalid @enderror"
                           value="{{ old('horas_semanales', $materia->horas_semanales ?? '') }}" min="1" max="40">
                    @error('horas_semanales') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Descripción</label>
                    <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $materia->descripcion ?? '') }}</textarea>
                    @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 24px;font-weight:600;"><i class="bi bi-save me-1"></i> {{ isset($materia) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.materias.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:8px 24px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

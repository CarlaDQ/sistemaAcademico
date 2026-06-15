@extends('layouts.app')

@section('title', isset($asignacion) ? 'Editar Asignación' : 'Nueva Asignación')

@section('content')
<div class="page-header mb-4">
    <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">{{ isset($asignacion) ? 'Editar Asignación' : 'Nueva Asignación' }}</h3>
    <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Complete los campos del formulario</p>
</div>
<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:16px 24px;font-weight:600;border:none;font-size:1rem;">{{ isset($asignacion) ? 'Editar Asignación' : 'Nueva Asignación' }}</div>
    <div class="card-body" style="padding:24px;">
        <form action="{{ isset($asignacion) ? route('admin.asignaciones.update', $asignacion->id_asignacion) : route('admin.asignaciones.store') }}" method="POST">
            @csrf
            @if(isset($asignacion)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Docente <span class="text-danger">*</span></label>
                    <select name="id_docente" class="form-select @error('id_docente') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($docentes as $d)
                        <option value="{{ $d->id_docente }}" {{ old('id_docente', $asignacion->id_docente ?? '') == $d->id_docente ? 'selected' : '' }}>
                            {{ $d->usuario->nombre ?? '' }} {{ $d->usuario->apellido ?? '' }} ({{ $d->codigo_docente }})
                        </option>
                        @endforeach
                    </select>
                    @error('id_docente') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Materia <span class="text-danger">*</span></label>
                    <select name="id_materia" class="form-select @error('id_materia') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($materias as $m)
                        <option value="{{ $m->id_materia }}" {{ old('id_materia', $asignacion->id_materia ?? '') == $m->id_materia ? 'selected' : '' }}>
                            {{ $m->nombre }} ({{ $m->codigo }})
                        </option>
                        @endforeach
                    </select>
                    @error('id_materia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Curso <span class="text-danger">*</span></label>
                    <select name="id_curso" class="form-select @error('id_curso') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($cursos as $c)
                        <option value="{{ $c->id_curso }}" {{ old('id_curso', $asignacion->id_curso ?? '') == $c->id_curso ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_curso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Año Lectivo <span class="text-danger">*</span></label>
                    <select name="id_anio" class="form-select @error('id_anio') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($anios as $a)
                        <option value="{{ $a->id_anio }}" {{ old('id_anio', $asignacion->id_anio ?? '') == $a->id_anio ? 'selected' : '' }}>
                            {{ $a->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_anio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 24px;font-weight:600;"><i class="bi bi-save me-1"></i> {{ isset($asignacion) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.asignaciones.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:8px 24px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

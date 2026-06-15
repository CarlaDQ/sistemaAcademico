@extends('layouts.app')

@section('title', isset($horario) ? 'Editar Horario' : 'Nuevo Horario')

@section('content')
<div class="page-header mb-4">
    <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">{{ isset($horario) ? 'Editar Horario' : 'Nuevo Horario' }}</h3>
    <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Complete los campos del formulario</p>
</div>
<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:16px 24px;font-weight:600;border:none;font-size:1rem;">{{ isset($horario) ? 'Editar Horario' : 'Nuevo Horario' }}</div>
    <div class="card-body" style="padding:24px;">
        <form action="{{ isset($horario) ? route('admin.horarios.update', $horario->id_horario) : route('admin.horarios.store') }}" method="POST">
            @csrf
            @if(isset($horario)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Asignación (Docente + Materia + Curso) <span class="text-danger">*</span></label>
                    <select name="id_asignacion" id="id_asignacion" class="form-select @error('id_asignacion') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($asignaciones as $a)
                        <option value="{{ $a->id_asignacion }}" {{ old('id_asignacion', $horario->id_asignacion ?? '') == $a->id_asignacion ? 'selected' : '' }}>
                            [{{ $a->codigo }}] {{ $a->docente->usuario->nombre ?? '' }} {{ $a->docente->usuario->apellido ?? '' }} -
                            {{ $a->materia->nombre ?? '' }} -
                            {{ $a->curso->nombre ?? '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_asignacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Día de la Semana <span class="text-danger">*</span></label>
                    <select name="dia_semana" class="form-select @error('dia_semana') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($dias as $d)
                        <option value="{{ $d }}" {{ old('dia_semana', $horario->dia_semana ?? '') == $d ? 'selected' : '' }}>
                            {{ ucfirst($d) }}
                        </option>
                        @endforeach
                    </select>
                    @error('dia_semana') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Hora Inicio <span class="text-danger">*</span></label>
                    <input type="time" name="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror"
                           value="{{ old('hora_inicio', isset($horario) ? substr($horario->hora_inicio, 0, 5) : '14:00') }}" required>
                    @error('hora_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Hora Fin <span class="text-danger">*</span></label>
                    <input type="time" name="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror"
                           value="{{ old('hora_fin', isset($horario) ? substr($horario->hora_fin, 0, 5) : '15:40') }}" required>
                    @error('hora_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-3 p-3 bg-light rounded" style="border-radius:10px;">
                <p class="mb-0 small text-muted">
                    <i class="bi bi-info-circle"></i>
                    Horario general: <strong>2:00 PM a 6:00 PM</strong> (Lunes a Sábado).
                    Recreo: <strong>4:00 PM a 4:30 PM</strong>.
                    Cada periodo dura aproximadamente <strong>40 minutos</strong>.
                </p>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 24px;font-weight:600;"><i class="bi bi-save me-1"></i> {{ isset($horario) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.horarios.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:8px 24px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

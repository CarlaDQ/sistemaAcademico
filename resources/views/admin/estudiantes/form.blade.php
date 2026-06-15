@extends('layouts.app')

@section('title', isset($estudiante) ? 'Editar Estudiante' : 'Nuevo Estudiante')

@section('content')
<div class="page-header mb-4">
    <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">{{ isset($estudiante) ? 'Editar Estudiante' : 'Nuevo Estudiante' }}</h3>
    <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Complete los campos del formulario</p>
</div>
<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:16px 24px;font-weight:600;border:none;font-size:1rem;">{{ isset($estudiante) ? 'Editar Estudiante' : 'Nuevo Estudiante' }}</div>
    <div class="card-body" style="padding:24px;">
        <form action="{{ isset($estudiante) ? route('admin.estudiantes.update', $estudiante->id_estudiante) : route('admin.estudiantes.store') }}" method="POST">
            @csrf
            @if(isset($estudiante)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $estudiante->usuario->nombre ?? '') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                           value="{{ old('apellido', $estudiante->usuario->apellido ?? '') }}" required>
                    @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">CI / Cédula <span class="text-danger">*</span></label>
                    <input type="text" name="ci" class="form-control @error('ci') is-invalid @enderror"
                           value="{{ old('ci', $estudiante->usuario->ci ?? '') }}" required placeholder="12345678">
                    @error('ci') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">La contraseña se genera como: CI + "davpin"</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Teléfono</label>
                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono', $estudiante->usuario->telefono ?? '') }}">
                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Contraseña (autogenerada)</label>
                    <input type="text" class="form-control" value="[CI] + davpin" readonly disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Curso <span class="text-danger">*</span></label>
                    <select name="id_curso" class="form-select @error('id_curso') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($cursos as $c)
                        <option value="{{ $c->id_curso }}" {{ old('id_curso', $estudiante->id_curso ?? '') == $c->id_curso ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_curso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Código (autogenerado)</label>
                    <input type="text" class="form-control" value="{{ $estudiante->codigo_estudiante ?? 'EST + ID' }}" readonly disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                           value="{{ old('fecha_nacimiento', isset($estudiante) && $estudiante->fecha_nacimiento ? $estudiante->fecha_nacimiento->format('Y-m-d') : '') }}">
                    @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Género</label>
                    <select name="genero" class="form-select @error('genero') is-invalid @enderror">
                        <option value="">Seleccione...</option>
                        <option value="masculino" {{ old('genero', $estudiante->genero ?? '') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="femenino" {{ old('genero', $estudiante->genero ?? '') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                        <option value="otro" {{ old('genero', $estudiante->genero ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
                        <option value="prefiero_no_decir" {{ old('genero', $estudiante->genero ?? '') == 'prefiero_no_decir' ? 'selected' : '' }}>Prefiero no decir</option>
                    </select>
                    @error('genero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <div class="card" style="border-radius:12px;border:1px solid #e9ecef;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                        <div class="card-body" style="padding:16px;">
                            <h6 style="color:#1e3c72;font-weight:600;font-size:0.95rem;margin-bottom:12px;"><i class="bi bi-journal-text me-1"></i> Materias que se asignarán</h6>
                            <p style="color:#888;font-size:0.8rem;margin-bottom:12px;">Las materias se asignan automáticamente según el curso seleccionado.</p>
                            <div class="row" id="materias-container">
                                @foreach($materias as $m)
                                @php
                                    $cursosDeMateria = $asignaciones->where('id_materia', $m->id_materia)->pluck('id_curso')->implode(',');
                                @endphp
                                <div class="col-md-3 mb-1 materia-item" data-cursos="{{ $cursosDeMateria }}" style="display:none;">
                                    <span class="badge bg-light text-dark" style="font-weight:400;font-size:0.85rem;padding:6px 10px;">{{ $m->nombre }}</span>
                                </div>
                                @endforeach
                                <div class="col-12" id="materias-empty">
                                    <p class="text-muted" style="font-size:0.85rem;">Seleccione un curso para ver las materias.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @if(!isset($estudiante))
            <div class="card mt-4" style="border-radius:12px;border:1px solid #e9ecef;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                <div class="card-body" style="padding:16px;">
                    <h6 style="color:#1e3c72;font-weight:600;font-size:0.95rem;margin-bottom:4px;">
                        <i class="bi bi-people-fill me-1"></i> Datos del Padre/Madre/Tutor
                    </h6>
                    <p style="color:#888;font-size:0.8rem;margin-bottom:12px;">Ingrese el CI del padre para buscarlo si ya existe, o complete los campos para crear uno nuevo.</p>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">CI del Padre/Madre</label>
                            <div class="input-group">
                                <input type="text" name="padre_ci" id="padre_ci" class="form-control" value="{{ old('padre_ci') }}" placeholder="12345678">
                                <button type="button" id="btn-buscar-padre" class="btn btn-outline-primary" style="border-radius:0 10px 10px 0;"><i class="bi bi-search"></i></button>
                            </div>
                            <div id="padre-resultado" style="font-size:0.8rem;margin-top:4px;"></div>
                        </div>
                        <input type="hidden" name="padre_existente" id="padre_existente" value="">
                        <div class="col-md-3">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nombre del Padre/Madre</label>
                            <input type="text" name="padre_nombre" id="padre_nombre" class="form-control" value="{{ old('padre_nombre') }}" placeholder="Nombre(s)">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Apellido del Padre/Madre</label>
                            <input type="text" name="padre_apellido" id="padre_apellido" class="form-control" value="{{ old('padre_apellido') }}" placeholder="Apellido(s)">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Telefono del Padre/Madre</label>
                            <input type="text" name="padre_telefono" id="padre_telefono" class="form-control" value="{{ old('padre_telefono') }}" placeholder="Telefono">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Parentesco</label>
                            <select name="padre_parentesco" id="padre_parentesco" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="padre" {{ old('padre_parentesco') == 'padre' ? 'selected' : '' }}>Padre</option>
                                <option value="madre" {{ old('padre_parentesco') == 'madre' ? 'selected' : '' }}>Madre</option>
                                <option value="tutor_legal" {{ old('padre_parentesco') == 'tutor_legal' ? 'selected' : '' }}>Tutor Legal</option>
                                <option value="abuelo" {{ old('padre_parentesco') == 'abuelo' ? 'selected' : '' }}>Abuelo(a)</option>
                                <option value="otro" {{ old('padre_parentesco') == 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Ocupacion</label>
                            <input type="text" name="padre_ocupacion" id="padre_ocupacion" class="form-control" value="{{ old('padre_ocupacion') }}" placeholder="Ocupacion">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 24px;font-weight:600;"><i class="bi bi-save me-1"></i> {{ isset($estudiante) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.estudiantes.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:8px 24px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var cursoSelect = document.querySelector('[name="id_curso"]');
    var emptyMsg = document.getElementById('materias-empty');
    if (cursoSelect) {
        cursoSelect.addEventListener('change', function() {
            var cursoId = this.value;
            var visibleCount = 0;
            document.querySelectorAll('.materia-item').forEach(function(el) {
                var cursos = (el.getAttribute('data-cursos') || '').split(',').filter(Boolean);
                if (!cursoId || cursos.includes(cursoId)) {
                    el.style.display = '';
                    if (cursoId) visibleCount++;
                } else {
                    el.style.display = 'none';
                }
            });
            if (emptyMsg) {
                emptyMsg.style.display = cursoId && visibleCount > 0 ? 'none' : '';
                if (cursoId && visibleCount === 0) {
                    emptyMsg.innerHTML = '<p class="text-muted" style="font-size:0.85rem;">No hay materias asignadas a este curso.</p>';
                } else if (!cursoId) {
                    emptyMsg.innerHTML = '<p class="text-muted" style="font-size:0.85rem;">Seleccione un curso para ver las materias.</p>';
                }
            }
        });
        cursoSelect.dispatchEvent(new Event('change'));
    }

    var btnBuscar = document.getElementById('btn-buscar-padre');
    var inputCi = document.getElementById('padre_ci');
    var resultado = document.getElementById('padre-resultado');

    if (btnBuscar && inputCi) {
        function buscarPadre() {
            var ci = inputCi.value.trim();
            if (!ci) {
                resultado.innerHTML = '';
                return;
            }
            resultado.innerHTML = '<span class="text-muted">Buscando...</span>';
            fetch('{{ route("admin.padres.buscar", "") }}/' + encodeURIComponent(ci))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.encontrado) {
                        document.getElementById('padre_existente').value = data.id_padre;
                        document.getElementById('padre_nombre').value = data.nombre;
                        document.getElementById('padre_apellido').value = data.apellido;
                        document.getElementById('padre_telefono').value = data.telefono || '';
                        document.getElementById('padre_parentesco').value = data.parentesco || '';
                        document.getElementById('padre_ocupacion').value = data.ocupacion || '';
                        resultado.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Padre encontrado: ' + data.nombre + ' ' + data.apellido + '</span>';
                    } else {
                        document.getElementById('padre_existente').value = '';
                        resultado.innerHTML = '<span class="text-warning">No encontrado. Complete los datos para crear uno nuevo.</span>';
                    }
                })
                .catch(function() {
                    resultado.innerHTML = '<span class="text-danger">Error al buscar.</span>';
                });
        }
        btnBuscar.addEventListener('click', buscarPadre);
        inputCi.addEventListener('blur', buscarPadre);
    }
});
</script>
@endpush

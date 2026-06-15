@extends('layouts.app')

@section('title', isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario')

@section('content')
<div class="page-header mb-4">
    <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">{{ isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario' }}</h3>
    <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Complete los campos del formulario</p>
</div>
<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:16px 24px;font-weight:600;border:none;font-size:1rem;">{{ isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario' }}</div>
    <div class="card-body" style="padding:24px;">
        <form action="{{ isset($usuario) ? route('admin.usuarios.update', $usuario->id_usuario) : route('admin.usuarios.store') }}" method="POST">
            @csrf
            @if(isset($usuario)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">CI / Cédula <span class="text-danger">*</span></label>
                    <input type="text" name="ci" class="form-control @error('ci') is-invalid @enderror"
                           value="{{ old('ci', $usuario->ci ?? '') }}" placeholder="12345678" required>
                    @error('ci') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">La contraseña se genera como: CI + "davpin"</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $usuario->nombre ?? '') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                           value="{{ old('apellido', $usuario->apellido ?? '') }}" required>
                    @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Email (autogenerado)</label>
                    <input type="text" class="form-control" value="{{ $usuario->email_decrypted ?? 'Se generará al crear' }}" readonly disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Contraseña (autogenerada)</label>
                    <input type="text" class="form-control" value="[CI] + davpin" readonly disabled>
                    @if(isset($usuario))
                    <small class="text-muted">Para cambiar, llena el campo de abajo.</small>
                    @endif
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Teléfono</label>
                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono', $usuario->telefono ?? '') }}">
                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Rol <span class="text-danger">*</span></label>
                    <select name="id_rol" id="id_rol" class="form-select @error('id_rol') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->id_rol }}" {{ old('id_rol', $usuario->id_rol ?? '') == $r->id_rol ? 'selected' : '' }}
                            data-nombre="{{ strtolower($r->nombre) }}">
                            {{ $r->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_rol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Estado</label>
                    <select name="estado" class="form-select @error('estado') is-invalid @enderror">
                        <option value="activo" {{ old('estado', $usuario->estado ?? '') === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $usuario->estado ?? '') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @if(isset($usuario))
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

                {{-- Campos específicos para Estudiante --}}
                <div id="campos-estudiante" style="display:none" class="w-100">
                    <hr>
                    <h6 style="color:#1e3c72;font-weight:600;font-size:0.95rem;margin-bottom:12px;"><i class="bi bi-person-graduate"></i> Datos del Estudiante</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Curso <span class="text-danger">*</span></label>
                            <select name="id_curso" class="form-select @error('id_curso') is-invalid @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($cursos as $c)
                                <option value="{{ $c->id_curso }}" {{ old('id_curso') == $c->id_curso ? 'selected' : '' }}>{{ $c->nombre }}</option>
                                @endforeach
                            </select>
                            @error('id_curso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento') }}">
                            @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Género</label>
                            <select name="genero" class="form-select @error('genero') is-invalid @enderror">
                                <option value="">Seleccione...</option>
                                <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="femenino" {{ old('genero') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="otro" {{ old('genero') == 'otro' ? 'selected' : '' }}>Otro</option>
                                <option value="prefiero_no_decir" {{ old('genero') == 'prefiero_no_decir' ? 'selected' : '' }}>Prefiero no decir</option>
                            </select>
                            @error('genero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <div class="card" style="border-radius:12px;border:1px solid #e9ecef;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                                <div class="card-body" style="padding:16px;">
                                    <h6 style="color:#1e3c72;font-weight:600;font-size:0.95rem;margin-bottom:12px;">Materias que se asignarán</h6>
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

                    @if(!isset($usuario))
                    <div class="card mt-3" style="border-radius:12px;border:1px solid #e9ecef;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
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
                                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Teléfono del Padre/Madre</label>
                                    <input type="text" name="padre_telefono" id="padre_telefono" class="form-control" value="{{ old('padre_telefono') }}" placeholder="Teléfono">
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
                                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Ocupación</label>
                                    <input type="text" name="padre_ocupacion" id="padre_ocupacion" class="form-control" value="{{ old('padre_ocupacion') }}" placeholder="Ocupación">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Campos específicos para Docente --}}
                <div id="campos-docente" style="display:none" class="w-100">
                    <hr>
                    <h6 style="color:#1e3c72;font-weight:600;font-size:0.95rem;margin-bottom:12px;"><i class="bi bi-person-workspace"></i> Datos del Docente</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Especialidad</label>
                            <input type="text" name="especialidad" class="form-control @error('especialidad') is-invalid @enderror" value="{{ old('especialidad') }}">
                            @error('especialidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Título Académico</label>
                            <input type="text" name="titulo_academico" class="form-control @error('titulo_academico') is-invalid @enderror" value="{{ old('titulo_academico') }}">
                            @error('titulo_academico') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Campos específicos para Padre de Familia --}}
                <div id="campos-padre" style="display:none" class="w-100">
                    <hr>
                    <h6 style="color:#1e3c72;font-weight:600;font-size:0.95rem;margin-bottom:12px;"><i class="bi bi-people-fill"></i> Datos del Padre de Familia</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Parentesco <span class="text-danger">*</span></label>
                            <select name="parentesco" class="form-select @error('parentesco') is-invalid @enderror">
                                <option value="">Seleccione...</option>
                                <option value="padre" {{ old('parentesco') == 'padre' ? 'selected' : '' }}>Padre</option>
                                <option value="madre" {{ old('parentesco') == 'madre' ? 'selected' : '' }}>Madre</option>
                                <option value="tutor_legal" {{ old('parentesco') == 'tutor_legal' ? 'selected' : '' }}>Tutor Legal</option>
                                <option value="abuelo" {{ old('parentesco') == 'abuelo' ? 'selected' : '' }}>Abuelo(a)</option>
                                <option value="otro" {{ old('parentesco') == 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('parentesco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Ocupación</label>
                            <input type="text" name="ocupacion" class="form-control @error('ocupacion') is-invalid @enderror" value="{{ old('ocupacion') }}">
                            @error('ocupacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <div class="card" style="border-radius:12px;border:1px solid #e9ecef;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                                <div class="card-body" style="padding:16px;">
                                    <h6 style="color:#1e3c72;font-weight:600;font-size:0.95rem;margin-bottom:4px;">Estudiantes vinculados</h6>
                                    <p style="color:#888;font-size:0.8rem;margin-bottom:12px;">Seleccione los estudiantes que estarán vinculados a este padre/tutor.</p>
                                    @if($cursos->isEmpty())
                                        <p class="text-warning">No hay cursos activos.</p>
                                    @else
                                        @foreach($cursos as $curso)
                                        <div class="mb-3">
                                            <label style="font-weight:600;color:#1e3c72;font-size:0.85rem;">{{ $curso->nombre }}</label>
                                            <div class="row row-cols-md-3 g-2 mt-1">
                                                @foreach($curso->estudiantes()->where('activo', true)->get() as $estudiante)
                                                <div class="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="estudiantes[]"
                                                               value="{{ $estudiante->id_estudiante }}"
                                                               id="est_{{ $estudiante->id_estudiante }}"
                                                               {{ in_array($estudiante->id_estudiante, old('estudiantes', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="est_{{ $estudiante->id_estudiante }}">
                                                            {{ $estudiante->usuario->nombre ?? '' }} {{ $estudiante->usuario->apellido ?? '' }}
                                                            <small class="text-muted">({{ $estudiante->codigo_estudiante }})</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 24px;font-weight:600;"><i class="bi bi-save me-1"></i> {{ isset($usuario) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:8px 24px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleRoles() {
    var sel = document.getElementById('id_rol');
    var opt = sel.options[sel.selectedIndex];
    var nombre = opt ? opt.getAttribute('data-nombre') : '';
    document.getElementById('campos-estudiante').style.display = nombre === 'estudiante' ? 'block' : 'none';
    document.getElementById('campos-docente').style.display = nombre === 'docente' ? 'block' : 'none';
    document.getElementById('campos-padre').style.display = nombre === 'padre_familia' ? 'block' : 'none';
    setTimeout(filtrarMaterias, 50);
}
document.getElementById('id_rol').addEventListener('change', toggleRoles);
toggleRoles();

function filtrarMaterias() {
    var cursoSelect = document.querySelector('#campos-estudiante [name="id_curso"]');
    var emptyMsg = document.getElementById('materias-empty');
    if (!cursoSelect) return;
    var cursoId = cursoSelect.value;
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
}

var cursoSelect = document.querySelector('#campos-estudiante [name="id_curso"]');
if (cursoSelect) {
    cursoSelect.addEventListener('change', filtrarMaterias);
    setTimeout(filtrarMaterias, 100);
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
</script>
@endsection

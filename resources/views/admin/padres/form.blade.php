@extends('layouts.app')

@section('title', isset($padre) ? 'Editar Padre de Familia' : 'Nuevo Padre de Familia')

@section('content')
<div class="page-header mb-4">
    <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">{{ isset($padre) ? 'Editar Padre de Familia' : 'Nuevo Padre de Familia' }}</h3>
    <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Complete los campos del formulario</p>
</div>
<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-header" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border-radius:14px 14px 0 0;padding:16px 24px;font-weight:600;border:none;font-size:1rem;">{{ isset($padre) ? 'Editar Padre de Familia' : 'Nuevo Padre de Familia' }}</div>
    <div class="card-body" style="padding:24px;">
        <form action="{{ isset($padre) ? route('admin.padres.update', $padre->id_padre) : route('admin.padres.store') }}" method="POST">
            @csrf
            @if(isset($padre)) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $padre->usuario->nombre ?? '') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                           value="{{ old('apellido', $padre->usuario->apellido ?? '') }}" required>
                    @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Teléfono</label>
                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono', $padre->usuario->telefono ?? '') }}">
                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Parentesco <span class="text-danger">*</span></label>
                    <select name="parentesco" class="form-select @error('parentesco') is-invalid @enderror" required>
                        <option value="">Seleccione...</option>
                        <option value="padre" {{ old('parentesco', $padre->parentesco ?? '') == 'padre' ? 'selected' : '' }}>Padre</option>
                        <option value="madre" {{ old('parentesco', $padre->parentesco ?? '') == 'madre' ? 'selected' : '' }}>Madre</option>
                        <option value="tutor_legal" {{ old('parentesco', $padre->parentesco ?? '') == 'tutor_legal' ? 'selected' : '' }}>Tutor Legal</option>
                        <option value="abuelo" {{ old('parentesco', $padre->parentesco ?? '') == 'abuelo' ? 'selected' : '' }}>Abuelo(a)</option>
                        <option value="otro" {{ old('parentesco', $padre->parentesco ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('parentesco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Ocupación</label>
                    <input type="text" name="ocupacion" class="form-control @error('ocupacion') is-invalid @enderror"
                           value="{{ old('ocupacion', $padre->ocupacion ?? '') }}">
                    @error('ocupacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Email (autogenerado)</label>
                    <input type="text" class="form-control" readonly disabled
                           value="{{ isset($padre) && $padre->usuario ? Crypt::decryptString($padre->usuario->email_cifrado) : '(se genera al guardar)' }}">
                </div>
                @if(!isset($padre))
                <div class="col-md-6">
                    <label class="form-label" style="font-weight:600;color:#444;font-size:0.85rem;">Contraseña (autogenerada)</label>
                    <input type="text" class="form-control" value="12345678" readonly disabled>
                </div>
                @endif
            </div>

            <div class="mt-4">
                <div class="card" style="border-radius:12px;border:1px solid #e9ecef;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                    <div class="card-body" style="padding:16px;">
                        <h6 style="color:#1e3c72;font-weight:600;font-size:0.95rem;margin-bottom:4px;">Estudiantes vinculados</h6>
                        <p style="color:#888;font-size:0.8rem;margin-bottom:12px;">Busque y seleccione los estudiantes que estarán vinculados a este padre/tutor.</p>

                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <select id="filter-curso" class="form-select form-select-sm">
                                    <option value="">Todos los cursos</option>
                                    @foreach($cursos as $curso)
                                        <option value="{{ $curso->id_curso }}">{{ $curso->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="search-estudiantes" class="form-control" placeholder="Buscar por nombre, CI o código...">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-search"><i class="bi bi-search"></i></button>
                                </div>
                            </div>
                        </div>

                        <div id="search-results" class="mb-3" style="max-height:200px;overflow-y:auto;display:none;"></div>

                        <div id="selected-estudiantes" class="d-flex flex-wrap gap-2 mb-2">
                            @if(isset($vinculados) && count($vinculados) > 0)
                                @php
                                    $selectedStudents = \App\Models\Estudiante::with('usuario','curso')->whereIn('id_estudiante', $vinculados)->get();
                                @endphp
                                @foreach($selectedStudents as $est)
                                <span class="badge d-inline-flex align-items-center gap-1 estudiante-tag" style="background:#e8f0fe;color:#1e3c72;font-size:0.8rem;padding:6px 10px;border-radius:20px;">
                                    {{ $est->usuario->nombre ?? '' }} {{ $est->usuario->apellido ?? '' }}
                                    <small>({{ $est->codigo_estudiante }})</small>
                                    <i class="bi bi-x-circle-fill remove-estudiante" data-id="{{ $est->id_estudiante }}" style="cursor:pointer;color:#999;font-size:0.75rem;"></i>
                                </span>
                                @endforeach
                            @endif
                        </div>
                        <div id="selected-ids-container"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 24px;font-weight:600;"><i class="bi bi-save me-1"></i> {{ isset($padre) ? 'Actualizar' : 'Guardar' }}</button>
                <a href="{{ route('admin.padres.index') }}" class="btn btn-outline-secondary" style="border-radius:10px;padding:8px 24px;">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-estudiantes');
    const filterCurso = document.getElementById('filter-curso');
    const btnSearch = document.getElementById('btn-search');
    const resultsDiv = document.getElementById('search-results');
    const selectedContainer = document.getElementById('selected-estudiantes');
    const hiddenContainer = document.getElementById('selected-ids-container');

    let selectedIds = [];
    let searchTimeout = null;

    // Load already selected students
    selectedIds = @json($vinculados ?? []);
    updateHiddenInputs();
    updateSelectedTags();

    function doSearch() {
        const q = searchInput.value.trim();
        const cursoId = filterCurso.value;

        if (q.length < 1 && !cursoId) {
            resultsDiv.style.display = 'none';
            return;
        }

        fetch(`{{ route('admin.padres.estudiantes.search') }}?q=${encodeURIComponent(q)}&id_curso=${cursoId}`)
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="text-muted p-2" style="font-size:0.85rem;">No se encontraron estudiantes.</div>';
                    resultsDiv.style.display = 'block';
                    return;
                }
                let html = '<div class="list-group list-group-flush">';
                data.forEach(e => {
                    const alreadySelected = selectedIds.includes(e.id);
                    html += `<button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2" style="font-size:0.85rem;${alreadySelected?'opacity:0.5;':''}" data-id="${e.id}" ${alreadySelected?'disabled':''}>
                        <span>${e.text}</span>
                        ${alreadySelected ? '<span class="badge bg-secondary">agregado</span>' : '<i class="bi bi-plus-circle text-primary"></i>'}
                    </button>`;
                });
                html += '</div>';
                resultsDiv.innerHTML = html;
                resultsDiv.style.display = 'block';

                resultsDiv.querySelectorAll('.list-group-item:not([disabled])').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = parseInt(this.dataset.id);
                        if (!selectedIds.includes(id)) {
                            selectedIds.push(id);
                            updateSelectedTags();
                            updateHiddenInputs();
                            resultsDiv.style.display = 'none';
                            searchInput.value = '';
                        }
                    });
                });
            });
    }

    function updateSelectedTags() {
        if (selectedIds.length === 0) {
            selectedContainer.innerHTML = '<p class="text-muted" style="font-size:0.85rem;margin-bottom:0;">Ningún estudiante seleccionado.</p>';
            return;
        }

        const params = selectedIds.map(id => 'ids[]=' + id).join('&');
        fetch(`{{ route('admin.padres.estudiantes.search') }}?${params}`)
            .then(r => r.json())
            .then(data => {
                let html = '';
                data.forEach(e => {
                    html += `<span class="badge d-inline-flex align-items-center gap-1 estudiante-tag" style="background:#e8f0fe;color:#1e3c72;font-size:0.8rem;padding:6px 10px;border-radius:20px;">
                        ${e.nombre} ${e.apellido} <small>(${e.codigo})</small>
                        <i class="bi bi-x-circle-fill remove-estudiante" data-id="${e.id}" style="cursor:pointer;color:#999;font-size:0.75rem;"></i>
                    </span>`;
                });
                selectedContainer.innerHTML = html;

                selectedContainer.querySelectorAll('.remove-estudiante').forEach(el => {
                    el.addEventListener('click', function () {
                        const id = parseInt(this.dataset.id);
                        selectedIds = selectedIds.filter(s => s !== id);
                        updateSelectedTags();
                        updateHiddenInputs();
                    });
                });
            });
    }

    function updateHiddenInputs() {
        hiddenContainer.innerHTML = '';
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'estudiantes[]';
            input.value = id;
            hiddenContainer.appendChild(input);
        });
    }

    // Debounced search on input
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(doSearch, 300);
    });

    filterCurso.addEventListener('change', function () {
        if (searchInput.value.trim().length > 0 || this.value) {
            doSearch();
        }
    });

    btnSearch.addEventListener('click', doSearch);

    // Close results on click outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#search-estudiantes') && !e.target.closest('#search-results') && !e.target.closest('#btn-search') && !e.target.closest('#filter-curso')) {
            resultsDiv.style.display = 'none';
        }
    });
});
</script>
@endpush

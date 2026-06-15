@extends('layouts.app')

@section('title', 'Geocercas - Docente')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    body { background: #f0f2f5; }
    .card-custom {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: none;
        margin-bottom: 24px;
    }
    .card-custom .card-header {
        background: linear-gradient(135deg, #1a73e8, #1557b0);
        color: #fff;
        border-radius: 12px 12px 0 0;
        padding: 16px 24px;
        font-weight: 600;
    }
    .btn-primary-custom {
        background: #1a73e8;
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-primary-custom:hover { background: #1557b0; transform: translateY(-1px); }
    .btn-primary-custom i { margin-right: 6px; }
    .btn-danger-custom {
        background: #dc3545;
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-danger-custom:hover { background: #c82333; transform: translateY(-1px); }
    .btn-edit {
        background: transparent;
        color: #1a73e8;
        border: 1.5px solid #1a73e8;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-edit:hover { background: #1a73e8; color: #fff; }
    .btn-location {
        background: #fff;
        color: #1a73e8;
        border: 2px solid #1a73e8;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    .btn-location:hover { background: #1a73e8; color: #fff; }
    .table th {
        background: #f8f9fa;
        border-bottom: 2px solid #1a73e8;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #555;
    }
    .table td { vertical-align: middle; font-size: 0.85rem; }
    .table tr:nth-child(even) { background: #f8faff; }
    .table tr:hover { background: #e3f2fd; }
    .day-badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 600;
        margin: 1px;
    }
    .day-badge.active-day { background: #e3f2fd; color: #1565c0; }
    .day-badge.inactive-day { background: #f0f0f0; color: #bbb; }
    .modal-header-custom {
        background: linear-gradient(135deg, #1a73e8, #1557b0);
        color: #fff;
        border-radius: 12px 12px 0 0;
    }
    .modal-header-custom .btn-close { filter: brightness(0) invert(1); }
    .modal-content { border-radius: 12px; border: none; }
    .form-control:focus, .form-select:focus {
        border-color: #1a73e8;
        box-shadow: 0 0 0 2px rgba(26,115,232,0.15);
    }
    .stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 16px 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        border-left: 4px solid #1a73e8;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .number { font-size: 1.5rem; font-weight: 700; color: #1a73e8; }
    .stat-card .label { font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
    .day-checkboxes label { margin-right: 8px; font-size: 0.85rem; }
    .day-checkboxes input[type="checkbox"] { margin-right: 3px; }
    .coord-input { font-family: monospace; font-size: 0.85rem; }
    .map-container {
        height: 280px;
        border-radius: 10px;
        border: 2px solid #e0e0e0;
        z-index: 1;
    }
    .map-container .leaflet-container { border-radius: 8px; }
    .coord-display {
        background: #f8faff;
        border-radius: 8px;
        padding: 6px 12px;
        font-family: monospace;
        font-size: 0.8rem;
        color: #555;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1a73e8;"><i class="bi bi-geo-alt-fill me-2"></i>Geocercas</h3>
            <p class="text-muted small mb-0">Gestione las geocercas para control de asistencia por ubicaci&oacute;n</p>
        </div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg"></i> Nueva Geocerca
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="label">Total Geocercas</div>
                <div class="number">{{ $geocercas->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #2e7d32;">
                <div class="label">Asignaciones</div>
                <div class="number" style="color: #2e7d32;">{{ $asignaciones->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #e65100;">
                <div class="label">Con Geocerca</div>
                <div class="number" style="color: #e65100;">{{ $asignaciones->filter(fn($a) => $a->geocercas->isNotEmpty())->count() }}</div>
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list me-2"></i>Geocercas Registradas</span>
            <span class="badge bg-light text-dark">{{ $geocercas->count() }} registros</span>
        </div>
        <div class="card-body">
            @if($geocercas->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-geo-alt" style="font-size: 3rem; color: #ddd;"></i>
                    <p class="mt-3">No hay geocercas registradas.</p>
                    <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="bi bi-plus-lg"></i> Crear primera geocerca
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Asignaci&oacute;n</th>
                                <th>Coordenadas</th>
                                <th>Radio</th>
                                <th>Horario</th>
                                <th>D&iacute;as</th>
                                <th style="width:120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($geocercas as $idx => $g)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ $g->nombre }}</strong>
                                    @if($g->descripcion)
                                    <br><small class="text-muted">{{ $g->descripcion }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $g->asignacion->materia->nombre ?? '—' }}</small>
                                    <br><small class="text-muted">{{ $g->asignacion->curso->nombre ?? '' }}</small>
                                </td>
                                <td class="coord-input">
                                    <small>{{ number_format($g->latitud_centro, 6) }}, {{ number_format($g->longitud_centro, 6) }}</small>
                                </td>
                                <td><small>{{ $g->radio_metros }}m</small></td>
                                <td>
                                    @if($g->horario_inicio && $g->horario_fin)
                                        <small>{{ substr($g->horario_inicio, 0, 5) }} - {{ substr($g->horario_fin, 0, 5) }}</small>
                                    @else
                                        <small class="text-muted">—</small>
                                    @endif
                                </td>
                                <td>
                                    @php $diasG = explode(',', $g->dias_semana); @endphp
                                    @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $d)
                                        <span class="day-badge {{ in_array($d, $diasG) ? 'active-day' : 'inactive-day' }}">
                                            {{ ucfirst(substr($d, 0, 3)) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    <button class="btn btn-edit btn-sm" onclick="editarGeocerca({{ $g->id_geocerca }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="{{ route('docente.geocercas.destroy', $g->id_geocerca) }}" style="display:inline;" onsubmit="return confirm('Eliminar esta geocerca?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger-custom btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nueva Geocerca</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('docente.geocercas.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asignaci&oacute;n <span class="text-danger">*</span></label>
                            <select name="id_asignacion" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                @foreach($asignaciones as $a)
                                <option value="{{ $a->id_asignacion }}">
                                    {{ $a->materia->nombre ?? '—' }} - {{ $a->curso->nombre ?? '—' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required maxlength="255" placeholder="Ej: Sal&oacute;n 1A">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripci&oacute;n</label>
                            <textarea name="descripcion" class="form-control" rows="2" maxlength="300" placeholder="Ubicaci&oacute;n opcional..."></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Radio (metros) <span class="text-danger">*</span></label>
                            <input type="number" name="radio_metros" id="createRadio" class="form-control" required min="10" max="1000" value="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Hora inicio</label>
                            <input type="time" name="horario_inicio" class="form-control" value="14:00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Hora fin</label>
                            <input type="time" name="horario_fin" class="form-control" value="17:50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">D&iacute;as <span class="text-danger">*</span></label>
                            <div class="day-checkboxes mt-1">
                                @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $d)
                                <label>
                                    <input type="checkbox" name="dias_semana[]" value="{{ $d }}"
                                        {{ in_array($d, ['lunes','martes','miercoles','jueves','viernes']) ? 'checked' : '' }}>
                                    {{ ucfirst($d) }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0">Ubicaci&oacute;n en el mapa</label>
                                <button type="button" class="btn btn-location btn-sm" onclick="getCreateLocation()">
                                    <i class="bi bi-crosshair"></i> Usar mi ubicaci&oacute;n
                                </button>
                            </div>
                            <div id="createMap" class="map-container"></div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Haga clic en el mapa o arrastre el marcador para definir la ubicaci&oacute;n</small>
                                <span id="createCoords" class="coord-display">—</span>
                            </div>
                            <input type="hidden" name="latitud_centro" id="createLat">
                            <input type="hidden" name="longitud_centro" id="createLng">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Geocerca</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Asignaci&oacute;n <span class="text-danger">*</span></label>
                            <select name="id_asignacion" id="editAsignacion" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                @foreach($asignaciones as $a)
                                <option value="{{ $a->id_asignacion }}">
                                    {{ $a->materia->nombre ?? '—' }} - {{ $a->curso->nombre ?? '—' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="editNombre" class="form-control" required maxlength="255">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripci&oacute;n</label>
                            <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2" maxlength="300"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Radio (metros) <span class="text-danger">*</span></label>
                            <input type="number" name="radio_metros" id="editRadio" class="form-control" required min="10" max="1000">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Hora inicio</label>
                            <input type="time" name="horario_inicio" id="editHoraInicio" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Hora fin</label>
                            <input type="time" name="horario_fin" id="editHoraFin" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">D&iacute;as <span class="text-danger">*</span></label>
                            <div class="day-checkboxes mt-1">
                                @foreach(['lunes','martes','miercoles','jueves','viernes','sabado'] as $d)
                                <label>
                                    <input type="checkbox" name="dias_semana[]" value="{{ $d }}" class="edit-day">
                                    {{ ucfirst($d) }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0">Ubicaci&oacute;n en el mapa</label>
                                <button type="button" class="btn btn-location btn-sm" onclick="getEditLocation()">
                                    <i class="bi bi-crosshair"></i> Usar mi ubicaci&oacute;n
                                </button>
                            </div>
                            <div id="editMap" class="map-container"></div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Arrastre el marcador o haga clic en el mapa para cambiar la ubicaci&oacute;n</small>
                                <span id="editCoords" class="coord-display">—</span>
                            </div>
                            <input type="hidden" name="latitud_centro" id="editLat">
                            <input type="hidden" name="longitud_centro" id="editLng">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save me-1"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var createMarker, createCircle, createMap;
var editMarker, editCircle, editMap;

function initCreateMap() {
    var defaultLat = -17.7833, defaultLng = -63.1822;
    createMap = L.map('createMap').setView([defaultLat, defaultLng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(createMap);

    createMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(createMap);
    createCircle = L.circle([defaultLat, defaultLng], {
        radius: parseInt(document.getElementById('createRadio').value) || 100,
        color: '#1a73e8',
        fillColor: '#1a73e8',
        fillOpacity: 0.1,
        weight: 2,
    }).addTo(createMap);

    function updateCreatePosition(lat, lng) {
        createMarker.setLatLng([lat, lng]);
        createCircle.setLatLng([lat, lng]);
        document.getElementById('createLat').value = lat.toFixed(7);
        document.getElementById('createLng').value = lng.toFixed(7);
        document.getElementById('createCoords').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    }

    updateCreatePosition(defaultLat, defaultLng);

    createMap.on('click', function(e) {
        updateCreatePosition(e.latlng.lat, e.latlng.lng);
    });
    createMarker.on('dragend', function() {
        var pos = createMarker.getLatLng();
        updateCreatePosition(pos.lat, pos.lng);
    });
    document.getElementById('createRadio').addEventListener('input', function() {
        createCircle.setRadius(parseInt(this.value) || 100);
    });
    setTimeout(function() { createMap.invalidateSize(); }, 300);
}

function initEditMap() {
    var defaultLat = -17.7833, defaultLng = -63.1822;
    editMap = L.map('editMap').setView([defaultLat, defaultLng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(editMap);

    editMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(editMap);
    editCircle = L.circle([defaultLat, defaultLng], {
        radius: 100,
        color: '#1a73e8',
        fillColor: '#1a73e8',
        fillOpacity: 0.1,
        weight: 2,
    }).addTo(editMap);

    function updateEditPosition(lat, lng) {
        editMarker.setLatLng([lat, lng]);
        editCircle.setLatLng([lat, lng]);
        document.getElementById('editLat').value = lat.toFixed(7);
        document.getElementById('editLng').value = lng.toFixed(7);
        document.getElementById('editCoords').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    }

    editMap.on('click', function(e) {
        updateEditPosition(e.latlng.lat, e.latlng.lng);
    });
    editMarker.on('dragend', function() {
        var pos = editMarker.getLatLng();
        updateEditPosition(pos.lat, pos.lng);
    });
    document.getElementById('editRadio').addEventListener('input', function() {
        editCircle.setRadius(parseInt(this.value) || 100);
    });
    setTimeout(function() { editMap.invalidateSize(); }, 300);
}

function getCreateLocation() {
    if (!navigator.geolocation) { alert('Geolocalizaci\u00f3n no soportada'); return; }
    navigator.geolocation.getCurrentPosition(function(pos) {
        var lat = pos.coords.latitude, lng = pos.coords.longitude;
        createMap.setView([lat, lng], 16);
        createMarker.setLatLng([lat, lng]);
        createCircle.setLatLng([lat, lng]);
        document.getElementById('createLat').value = lat.toFixed(7);
        document.getElementById('createLng').value = lng.toFixed(7);
        document.getElementById('createCoords').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    }, function() { alert('No se pudo obtener la ubicaci\u00f3n'); });
}

function getEditLocation() {
    if (!navigator.geolocation) { alert('Geolocalizaci\u00f3n no soportada'); return; }
    navigator.geolocation.getCurrentPosition(function(pos) {
        var lat = pos.coords.latitude, lng = pos.coords.longitude;
        editMap.setView([lat, lng], 16);
        editMarker.setLatLng([lat, lng]);
        editCircle.setLatLng([lat, lng]);
        document.getElementById('editLat').value = lat.toFixed(7);
        document.getElementById('editLng').value = lng.toFixed(7);
        document.getElementById('editCoords').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    }, function() { alert('No se pudo obtener la ubicaci\u00f3n'); });
}

document.getElementById('createModal').addEventListener('shown.bs.modal', function () {
    if (!createMap) initCreateMap();
    else setTimeout(function() { createMap.invalidateSize(); }, 200);
});

document.getElementById('editModal').addEventListener('shown.bs.modal', function () {
    if (!editMap) initEditMap();
    else setTimeout(function() { editMap.invalidateSize(); }, 200);
});

function editarGeocerca(id) {
    fetch('{{ url("docente/geocercas") }}/' + id + '/edit')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var g = data.geocerca;
            document.getElementById('editForm').action = '{{ url("docente/geocercas") }}/' + id;
            document.getElementById('editAsignacion').value = g.id_asignacion;
            document.getElementById('editNombre').value = g.nombre;
            document.getElementById('editDescripcion').value = g.descripcion || '';
            document.getElementById('editRadio').value = g.radio_metros;
            document.getElementById('editHoraInicio').value = g.horario_inicio ? g.horario_inicio.substring(0,5) : '';
            document.getElementById('editHoraFin').value = g.horario_fin ? g.horario_fin.substring(0,5) : '';

            var checkboxes = document.querySelectorAll('#editModal .edit-day');
            checkboxes.forEach(function(cb) { cb.checked = false; });
            data.dias_seleccionados.forEach(function(d) {
                checkboxes.forEach(function(cb) {
                    if (cb.value === d) cb.checked = true;
                });
            });

            document.getElementById('editLat').value = g.latitud_centro;
            document.getElementById('editLng').value = g.longitud_centro;
            document.getElementById('editCoords').textContent =
                parseFloat(g.latitud_centro).toFixed(6) + ', ' + parseFloat(g.longitud_centro).toFixed(6);

            if (editMap) {
                var lat = parseFloat(g.latitud_centro), lng = parseFloat(g.longitud_centro);
                editMap.setView([lat, lng], 16);
                editMarker.setLatLng([lat, lng]);
                editCircle.setLatLng([lat, lng]);
                editCircle.setRadius(parseInt(g.radio_metros) || 100);
            }

            var modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        })
        .catch(function() { alert('Error al cargar la geocerca'); });
}
</script>
@endpush

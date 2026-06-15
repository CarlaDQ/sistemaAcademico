@extends('layouts.app')

@section('title', 'Calificaciones - Docente')

@push('styles')
<style>
    body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }

    .spreadsheet-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .spreadsheet-header {
        background: linear-gradient(135deg, #1a73e8, #1557b0);
        color: #fff;
        padding: 20px 24px;
    }
    .spreadsheet-header h3 { margin: 0; font-weight: 700; font-size: 1.3rem; }

    .spreadsheet-toolbar {
        background: #f8f9fa;
        padding: 16px 24px;
        border-bottom: 1px solid #dee2e6;
    }
    .spreadsheet-toolbar .form-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .spreadsheet-toolbar .form-select, .spreadsheet-toolbar .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 0.9rem;
    }
    .spreadsheet-toolbar .form-select:focus, .spreadsheet-toolbar .form-control:focus {
        border-color: #1a73e8;
        box-shadow: 0 0 0 2px rgba(26,115,232,0.15);
    }

    .table-wrap {
        overflow-x: auto;
        padding: 0;
        position: relative;
    }

    .table-wrap table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: max-content;
        min-width: 100%;
    }

    .table-wrap table thead th {
        background: #f0f4ff;
        border-bottom: 2px solid #1a73e8;
        padding: 12px 12px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #333;
        text-align: center;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table-wrap table thead th.fixed-col {
        position: sticky;
        left: 0;
        z-index: 11;
        background: #e8eaf6;
    }

    .table-wrap table thead th.col-actions {
        z-index: 12;
    }

    .table-wrap table td {
        padding: 8px 10px;
        border-bottom: 1px solid #eee;
        text-align: center;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .table-wrap table td.fixed-col {
        position: sticky;
        left: 0;
        z-index: 1;
        background: #fff;
        font-weight: 500;
        text-align: left;
        min-width: 180px;
        border-right: 1px solid #e0e0e0;
    }

    .table-wrap table tr:nth-child(even) td:not(.fixed-col) { background: #f8faff; }
    .table-wrap table tr:nth-child(even) td.fixed-col { background: #f8faff; }
    .table-wrap table tr:hover td { background: #e3f2fd !important; }

    .student-search-box {
        position: relative;
    }
    .student-search-box input {
        padding-left: 32px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-size: 0.85rem;
    }
    .student-search-box input:focus {
        border-color: #1a73e8;
        box-shadow: 0 0 0 2px rgba(26,115,232,0.15);
    }
    .student-search-box .bi-search {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
    }

    .grade-input {
        width: 62px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 5px 3px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
        background: #fafafa;
    }
    .grade-input:focus {
        border-color: #1a73e8;
        box-shadow: 0 0 0 3px rgba(26,115,232,0.2);
        outline: none;
        background: #fff;
        transform: scale(1.05);
    }
    .grade-input.high { background: #e8f5e9; color: #2e7d32; border-color: #a5d6a7; }
    .grade-input.mid { background: #fff3e0; color: #e65100; border-color: #ffcc80; }
    .grade-input.low { background: #ffebee; color: #c62828; border-color: #ef9a9a; }

    .course-avg-row td { background: #e8eaf6 !important; font-weight: 700; border-top: 2px solid #1a73e8; }
    .course-avg-row .avg-label { color: #1a73e8; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }

    .student-row { transition: background 0.15s; }
    .student-row.row-hidden { display: none; }

    .activity-name-input {
        border: 1px solid transparent;
        background: transparent;
        font-weight: 700;
        text-align: center;
        width: 90px;
        padding: 6px 4px;
        border-radius: 6px;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    .activity-name-input:hover { border-color: #ccc; background: #fff; }
    .activity-name-input:focus { border-color: #1a73e8; background: #fff; outline: none; box-shadow: 0 0 0 2px rgba(26,115,232,0.15); }

    .avg-cell {
        font-weight: 700;
        color: #1a73e8;
        background: #e3f2fd !important;
        border-left: 2px solid #1a73e8;
        font-size: 1rem;
    }

    .btn-add-activity {
        border: 2px dashed #1a73e8;
        color: #1a73e8;
        background: transparent;
        padding: 6px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
        cursor: pointer;
        white-space: nowrap;
    }
    .btn-add-activity:hover { background: #1a73e8; color: #fff; }

    .btn-save-all {
        background: #1a73e8;
        color: #fff;
        border: none;
        padding: 8px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .btn-save-all:hover { background: #1557b0; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(26,115,232,0.3); }
    .btn-save-all:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .student-number { color: #999; font-size: 0.8rem; min-width: 30px; display: inline-block; }

    #loadingOverlay {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(255,255,255,0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    #loadingOverlay.show { display: flex; }

    .toast-container { z-index: 99999; }

    .col-actions { min-width: 50px; }

    .btn-remove-activity {
        color: #dc3545;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        padding: 2px 6px;
        border-radius: 4px;
        opacity: 0.5;
        transition: all 0.2s;
    }
    .btn-remove-activity:hover { opacity: 1; background: #ffebee; }

    .stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 14px 18px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        border-left: 4px solid #1a73e8;
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .number { font-size: 1.3rem; font-weight: 700; color: #1a73e8; }
    .stat-card .label { font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }

    .table-wrap table thead th .activity-header-wrap {
        display: flex;
        align-items: center;
        gap: 4px;
        justify-content: center;
    }

    .toast-custom .toast-header {
        background: #1a73e8;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div id="loadingOverlay">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1a73e8;"><i class="bi bi-table me-2"></i>Libreta de Calificaciones</h3>
            <p class="text-muted small mb-0">Ingrese y gestione las calificaciones de sus estudiantes</p>
        </div>
        <span class="badge bg-light text-dark fs-6 px-3 py-2" id="statusBadge" style="border: 1px solid #ddd; border-radius: 20px;">
            <i class="bi bi-check-circle text-success me-1"></i> Listo
        </span>
    </div>

    <div class="row g-3 mb-4" id="statsRow" style="display: none;">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="label">Estudiantes</div>
                <div class="number" id="statStudents">0</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #2e7d32;">
                <div class="label">Actividades</div>
                <div class="number" id="statActivities" style="color: #2e7d32;">0</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #e65100;">
                <div class="label">Prom. Curso</div>
                <div class="number" id="statAverage" style="color: #e65100;">-</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #7b1fa2;">
                <div class="label">Aprobados</div>
                <div class="number" id="statPassed" style="color: #7b1fa2;">0</div>
            </div>
        </div>
    </div>

    <div class="spreadsheet-container">
        <div class="spreadsheet-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3><i class="bi bi-table me-2"></i><span id="headerTitle">Libreta de Calificaciones</span></h3>
            </div>
        </div>

        <div class="spreadsheet-toolbar">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Asignaci&oacute;n</label>
                    <select id="asignacionSelect" class="form-select form-select-sm">
                        <option value="">Seleccionar asignaci&oacute;n...</option>
                        @foreach($asignaciones as $a)
                            <option value="{{ $a->id_asignacion }}" data-materia="{{ $a->materia->nombre }}" data-curso="{{ $a->curso->nombre }}">
                                {{ $a->materia->nombre }} - {{ $a->curso->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Periodo</label>
                    <select id="periodoSelect" class="form-select form-select-sm">
                        <option value="">Seleccionar periodo...</option>
                        @foreach($periodos as $p)
                            <option value="{{ $p->id_periodo }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Buscar estudiante</label>
                    <div class="student-search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" id="studentFilter" class="form-control form-control-sm" placeholder="Nombre o CI..." oninput="filterStudentsTable()">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn-add-activity btn-sm" onclick="addActivity()">
                        <i class="bi bi-plus-lg me-1"></i> Actividad
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end justify-content-end">
                    <button class="btn-save-all" id="saveAllBtn" onclick="saveAll()">
                        <i class="bi bi-save me-1"></i> Guardar Todo
                    </button>
                </div>
            </div>
        </div>

        <div class="table-wrap" id="tableContainer">
            <div class="text-center py-5 text-muted">
                <i class="bi bi-arrow-up-circle" style="font-size: 2.5rem; color: #ddd;"></i>
                <p class="mt-2">Seleccione una asignaci&oacute;n y periodo para comenzar</p>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="saveToast" class="toast toast-custom" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-check-circle text-success me-2"></i>
            <strong class="me-auto">Calificaciones</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let actividades = [];
    let estudiantes = [];
    let calificacionesData = {};
    let currentAsignacion = null;
    let currentPeriodo = null;

    $(document).ready(function () {
        $('#asignacionSelect').change(loadData);
        $('#periodoSelect').change(loadData);
    });

    function loadData() {
        const asignacionId = $('#asignacionSelect').val();
        const periodoId = $('#periodoSelect').val();

        if (!asignacionId || !periodoId) {
            $('#tableContainer').html(`
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-arrow-up-circle" style="font-size: 2.5rem; color: #ddd;"></i>
                    <p class="mt-2">Seleccione una asignaci&oacute;n y periodo para comenzar</p>
                </div>
            `);
            $('#statsRow').hide();
            $('#statusBadge').html('<i class="bi bi-info-circle text-secondary me-1"></i> Esperando selecci&oacute;n');
            return;
        }

        currentAsignacion = asignacionId;
        currentPeriodo = periodoId;
        showLoading(true);

        const materia = $('#asignacionSelect option:selected').data('materia');
        const curso = $('#asignacionSelect option:selected').data('curso');
        $('#headerTitle').text(materia + ' - ' + curso);

        $.get('{{ url("docente/calificaciones/actividades") }}/' + asignacionId, function (data) {
            estudiantes = data.estudiantes || [];
            calificacionesData = data.calificaciones || {};

            if (data.actividades && data.actividades.length > 0) {
                actividades = data.actividades.map(a => ({ nombre: a.nombre, id: a.id_tipo }));
            } else {
                actividades = [
                    { nombre: 'Tarea 1', id: null },
                    { nombre: 'Tarea 2', id: null },
                    { nombre: 'Examen Parcial', id: null },
                ];
            }

            renderTable();
            updateStats();
            showLoading(false);
            $('#statsRow').show();
            $('#statusBadge').html(`<i class="bi bi-check-circle text-success me-1"></i> ${estudiantes.length} estudiantes, ${actividades.length} actividades`);
        }).fail(function () {
            showLoading(false);
            $('#statsRow').hide();
            $('#tableContainer').html(`
                <div class="text-center py-5 text-danger">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                    <p class="mt-2 fw-bold">Error al cargar datos</p>
                    <p class="small">Verifique la conexi&oacute;n e intente nuevamente.</p>
                    <button class="btn btn-outline-danger btn-sm mt-2" onclick="loadData()">
                        <i class="bi bi-arrow-clockwise"></i> Reintentar
                    </button>
                </div>
            `);
            $('#statusBadge').html('<i class="bi bi-exclamation-circle text-danger me-1"></i> Error');
        });
    }

    function updateStats() {
        $('#statStudents').text(estudiantes.length);
        $('#statActivities').text(actividades.length);

        let totalProm = 0;
        let count = 0;
        let passed = 0;

        estudiantes.forEach(function (est) {
            const califEst = calificacionesData[est.id] || [];
            let suma = 0;
            let c = 0;
            actividades.forEach(function (act) {
                const calif = califEst.find(cf => cf.id_tipo_eval === act.id) || {};
                const nota = calif.nota !== undefined && calif.nota !== null ? parseFloat(calif.nota) : null;
                if (nota !== null) { suma += nota; c++; }
            });
            if (c > 0) {
                const prom = suma / c;
                totalProm += prom;
                count++;
                if (prom >= 70) passed++;
            }
        });

        $('#statAverage').text(count > 0 ? (totalProm / count).toFixed(1) : '-');
        $('#statPassed').text(passed);
    }

    function renderTable() {
        if (!estudiantes.length) {
            $('#tableContainer').html(`
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people" style="font-size: 2.5rem; color: #ddd;"></i>
                    <p class="mt-2">No hay estudiantes en este curso</p>
                </div>
            `);
            return;
        }

        let html = '<table class="table"><thead><tr>';
        html += '<th class="fixed-col" style="min-width:40px; text-align:center;">#</th>';
        html += '<th class="fixed-col" style="min-width:70px;">CI</th>';
        html += '<th class="fixed-col" style="min-width:200px;">Estudiante</th>';

        actividades.forEach(function (act, idx) {
            html += `<th style="min-width:100px;text-align:center;">
                <div class="activity-header-wrap">
                    <input type="text" class="activity-name-input" value="${act.nombre}"
                           data-index="${idx}" placeholder="Act. ${idx+1}">
                    <button class="btn-remove-activity" onclick="removeActivity(${idx})" title="Eliminar">&times;</button>
                </div>
            </th>`;
        });

        html += '<th class="avg-cell" style="min-width:60px;">Prom.</th>';
        html += '</tr></thead><tbody>';

        estudiantes.forEach(function (est, estIdx) {
            const califEst = calificacionesData[est.id] || [];
            html += `<tr class="student-row" data-name="${(est.nombre + ' ' + (est.ci || '')).toLowerCase()}">`;
            html += `<td class="fixed-col" style="text-align:center;"><span class="student-number">${estIdx + 1}</span></td>`;
            html += `<td class="fixed-col" style="font-size:0.8rem;color:#666;">${est.ci || '—'}</td>`;
            html += `<td class="fixed-col"><strong>${est.nombre}</strong></td>`;

            let suma = 0;
            let count = 0;

            actividades.forEach(function (act, actIdx) {
                const calif = califEst.find(c => c.id_tipo_eval === act.id) || {};
                const nota = calif.nota !== undefined && calif.nota !== null ? calif.nota : '';
                if (nota !== '') { suma += parseFloat(nota); count++; }

                const colorClass = nota !== '' && nota !== null ? (nota >= 70 ? 'high' : (nota >= 40 ? 'mid' : 'low')) : '';
                html += `<td>
                    <input type="number" class="grade-input ${colorClass}"
                           value="${nota}" min="0" max="100" step="0.5"
                           data-estudiante="${est.id}" data-actividad="${actIdx}"
                           oninput="onGradeChange(this)" onkeydown="onGradeKeydown(event, this)">
                </td>`;
            });

            const promedio = count > 0 ? (suma / count).toFixed(1) : '-';
            const promClass = promedio !== '-' ? (parseFloat(promedio) >= 70 ? 'text-success' : (parseFloat(promedio) >= 40 ? 'text-warning' : 'text-danger')) : '';
            html += `<td class="avg-cell"><strong class="${promClass}">${promedio}</strong></td>`;
            html += '</tr>';
        });

        // Course average row
        html += '<tr class="course-avg-row"><td class="fixed-col" colspan="3"><span class="avg-label"><i class="bi bi-calculator me-1"></i>Promedio del curso</span></td>';
        let actSums = [];
        let actCounts = [];
        actividades.forEach(function (act, idx) {
            actSums[idx] = 0;
            actCounts[idx] = 0;
        });
        estudiantes.forEach(function (est) {
            const califEst = calificacionesData[est.id] || [];
            actividades.forEach(function (act, idx) {
                const calif = califEst.find(c => c.id_tipo_eval === act.id) || {};
                const nota = calif.nota !== undefined && calif.nota !== null ? parseFloat(calif.nota) : null;
                if (nota !== null) { actSums[idx] += nota; actCounts[idx]++; }
            });
        });
        actividades.forEach(function (act, idx) {
            const avg = actCounts[idx] > 0 ? (actSums[idx] / actCounts[idx]).toFixed(1) : '-';
            const avgClass = avg !== '-' ? (parseFloat(avg) >= 70 ? 'text-success' : (parseFloat(avg) >= 40 ? 'text-warning' : 'text-danger')) : '';
            html += `<td style="text-align:center;"><strong class="${avgClass}" style="font-size:0.9rem;">${avg}</strong></td>`;
        });
        // Overall course average
        let totalSum = 0, totalCount = 0;
        actSums.forEach(function (s, i) { totalSum += s; totalCount += actCounts[i]; });
        const overallAvg = totalCount > 0 ? (totalSum / totalCount).toFixed(1) : '-';
        const overallClass = overallAvg !== '-' ? (parseFloat(overallAvg) >= 70 ? 'text-success' : (parseFloat(overallAvg) >= 40 ? 'text-warning' : 'text-danger')) : '';
        html += `<td style="text-align:center;background:#c5cae9 !important;"><strong class="${overallClass}" style="font-size:1rem;">${overallAvg}</strong></td>`;
        html += '</tr>';

        html += '</tbody></table>';
        $('#tableContainer').html(html);
        $('#studentFilter').val('');
    }

    function filterStudentsTable() {
        const query = document.getElementById('studentFilter').value.toLowerCase();
        document.querySelectorAll('.student-row').forEach(function (row) {
            const name = row.getAttribute('data-name') || '';
            row.classList.toggle('row-hidden', query && !name.includes(query));
        });
    }

    function onGradeChange(input) {
        const val = parseFloat(input.value);
        input.classList.remove('high', 'mid', 'low');
        if (input.value !== '') {
            if (val >= 70) input.classList.add('high');
            else if (val >= 40) input.classList.add('mid');
            else input.classList.add('low');
        }
        updateAverages();
        updateStatsFromInputs();
        $('#statusBadge').html('<i class="bi bi-pencil-square text-warning me-1"></i> Sin guardar...');
    }

    function updateStatsFromInputs() {
        const rows = document.querySelectorAll('#tableContainer tbody tr');
        let totalProm = 0;
        let count = 0;
        let passed = 0;

        rows.forEach(function (row) {
            const inputs = row.querySelectorAll('.grade-input');
            let suma = 0;
            let c = 0;
            inputs.forEach(function (inp) {
                const v = parseFloat(inp.value);
                if (!isNaN(v)) { suma += v; c++; }
            });
            if (c > 0) {
                const prom = suma / c;
                totalProm += prom;
                count++;
                if (prom >= 70) passed++;
            }
        });

        $('#statStudents').text(estudiantes.length);
        $('#statActivities').text(actividades.length);
        $('#statAverage').text(count > 0 ? (totalProm / count).toFixed(1) : '-');
        $('#statPassed').text(passed);
    }

    function updateAverages() {
        const rows = document.querySelectorAll('#tableContainer tbody tr');
        rows.forEach(function (row) {
            const inputs = row.querySelectorAll('.grade-input');
            let suma = 0, count = 0;
            inputs.forEach(function (inp) {
                const v = parseFloat(inp.value);
                if (!isNaN(v)) { suma += v; count++; }
            });
            const avgCell = row.querySelector('.avg-cell strong');
            if (avgCell) {
                const prom = count > 0 ? (suma / count).toFixed(1) : '-';
                avgCell.textContent = prom;
                avgCell.className = prom !== '-' ? (parseFloat(prom) >= 70 ? 'text-success' : (parseFloat(prom) >= 40 ? 'text-warning' : 'text-danger')) : '';
            }
        });
    }

    function onGradeKeydown(event, input) {
        if (event.key === 'Tab' || event.key === 'Enter') {
            event.preventDefault();
            const inputs = Array.from(document.querySelectorAll('.grade-input'));
            const idx = inputs.indexOf(input);
            const next = (event.shiftKey ? inputs[idx - 1] : inputs[idx + 1]);
            if (next) { next.focus(); next.select(); }
        }
    }

    function addActivity() {
        const idx = actividades.length;
        actividades.push({ nombre: `Actividad ${idx + 1}`, id: null });
        renderTable();
        $('#statusBadge').html('<i class="bi bi-pencil-square text-warning me-1"></i> Sin guardar...');
    }

    function removeActivity(index) {
        if (actividades.length <= 1) {
            alert('Debe haber al menos una actividad.');
            return;
        }
        if (!confirm('Eliminar esta actividad?')) return;
        actividades.splice(index, 1);
        renderTable();
        $('#statusBadge').html('<i class="bi bi-pencil-square text-warning me-1"></i> Sin guardar...');
    }

    function saveAll() {
        if (!currentAsignacion || !currentPeriodo) {
            alert('Seleccione asignaci&oacute;n y periodo.');
            return;
        }

        const activityNames = [];
        document.querySelectorAll('.activity-name-input').forEach(function (inp) {
            activityNames.push({ nombre: inp.value.trim() || 'Sin nombre' });
        });

        if (activityNames.length === 0) {
            alert('Debe definir al menos una actividad.');
            return;
        }

        const notas = {};
        document.querySelectorAll('#tableContainer tbody tr').forEach(function (row) {
            const estId = row.querySelector('.grade-input')?.dataset?.estudiante;
            if (!estId) return;
            notas[estId] = {};
            row.querySelectorAll('.grade-input').forEach(function (inp) {
                const actIdx = inp.dataset.actividad;
                notas[estId][actIdx] = inp.value !== '' ? inp.value : null;
            });
        });

        const btn = document.getElementById('saveAllBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

        $.ajax({
            url: '{{ url("docente/calificaciones") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id_asignacion: currentAsignacion,
                id_periodo: currentPeriodo,
                actividades: activityNames,
                notas: notas,
            },
            success: function (res) {
                showToast('success', 'Calificaciones guardadas correctamente.');
                $('#statusBadge').html('<i class="bi bi-check-circle text-success me-1"></i> Guardado');
                loadData();
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'Error al guardar calificaciones.';
                showToast('danger', msg);
                $('#statusBadge').html('<i class="bi bi-exclamation-circle text-danger me-1"></i> Error');
            },
            complete: function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-save me-1"></i> Guardar Todo';
            }
        });
    }

    function showLoading(show) {
        document.getElementById('loadingOverlay').classList.toggle('show', show);
    }

    function showToast(type, message) {
        const toastEl = document.getElementById('saveToast');
        const icon = type === 'success' ? 'bi-check-circle text-success' : 'bi-x-circle text-danger';
        toastEl.querySelector('.toast-header i').className = `bi ${icon} me-2`;
        toastEl.querySelector('.toast-body').textContent = message;
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    }
</script>
@endpush
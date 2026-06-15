@extends('layouts.app')

@section('title', 'Asignaciones - U.E. David Pinilla')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">Asignaciones</h3>
        <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Gesti&oacute;n de Asignaciones del sistema</p>
    </div>
    <a href="{{ route('admin.asignaciones.create') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 20px;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Nueva Asignación
    </a>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="id_docente" class="form-select form-select-sm">
                    <option value="">Todos los docentes</option>
                    @foreach($docentes as $d)
                    <option value="{{ $d->id_docente }}" {{ request('id_docente') == $d->id_docente ? 'selected' : '' }}>
                        {{ $d->usuario->nombre ?? '' }} {{ $d->usuario->apellido ?? '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="id_curso" class="form-select form-select-sm">
                    <option value="">Todos los cursos</option>
                    @foreach($cursos as $c)
                    <option value="{{ $c->id_curso }}" {{ request('id_curso') == $c->id_curso ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="id_anio" class="form-select form-select-sm">
                    <option value="">Todos los años</option>
                    @foreach($anios as $a)
                    <option value="{{ $a->id_anio }}" {{ request('id_anio') == $a->id_anio ? 'selected' : '' }}>{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm" type="submit" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-weight:600;"><i class="bi bi-search me-1"></i> Filtrar</button>
                <a href="{{ route('admin.asignaciones.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm datatable">
                <thead style="background:#f4f6f9;">
                    <tr style="border-bottom:2px solid #1e3c72;">
                        <th>Código</th>
                        <th>Docente</th>
                        <th>Materia</th>
                        <th>Curso</th>
                        <th>Año</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asignaciones as $a)
                    <tr>
                        <td><span class="badge bg-dark" style="border-radius:50px;padding:6px 12px;">{{ $a->codigo }}</span></td>
                        <td>{{ $a->docente->usuario->nombre ?? '—' }} {{ $a->docente->usuario->apellido ?? '' }}</td>
                        <td>{{ $a->materia->nombre ?? '—' }}</td>
                        <td>{{ $a->curso->nombre ?? '—' }}</td>
                        <td>{{ $a->anioLectivo->nombre ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.asignaciones.edit', $a->id_asignacion) }}" class="btn btn-sm" title="Editar" style="background:#e8f0fe;color:#1e3c72;border:none;border-radius:8px;padding:6px 10px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.asignaciones.destroy', $a->id_asignacion) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar esta asignación?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" title="Desactivar" style="background:#fce4ec;color:#c62828;border:none;border-radius:8px;padding:6px 10px;"><i class="bi bi-x-lg"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay asignaciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $asignaciones->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.datatable').DataTable({
        paging: false, info: false, searching: false,
        language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' }
    });
});
</script>
@endpush

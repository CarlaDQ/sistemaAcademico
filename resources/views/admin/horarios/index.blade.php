@extends('layouts.app')

@section('title', 'Horarios - U.E. David Pinilla')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">Horarios</h3>
        <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Gesti&oacute;n de Horarios del sistema</p>
    </div>
    <a href="{{ route('admin.horarios.create') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 20px;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Horario
    </a>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
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
            <div class="col-md-2">
                <button class="btn btn-sm" type="submit" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-weight:600;"><i class="bi bi-search me-1"></i> Filtrar</button>
                <a href="{{ route('admin.horarios.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-x-lg"></i></a>
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
                        <th>Día</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($horarios as $h)
                    <tr>
                        <td><span class="badge bg-dark" style="border-radius:50px;padding:6px 12px;">{{ $h->asignacion->codigo }}</span></td>
                        <td>{{ $h->asignacion->docente->usuario->nombre ?? '' }} {{ $h->asignacion->docente->usuario->apellido ?? '' }}</td>
                        <td>{{ $h->asignacion->materia->nombre ?? '—' }}</td>
                        <td>{{ $h->asignacion->curso->nombre ?? '—' }}</td>
                        <td><span class="badge bg-secondary" style="border-radius:50px;padding:6px 12px;">{{ ucfirst($h->dia_semana) }}</span></td>
                        <td>{{ substr($h->hora_inicio, 0, 5) }}</td>
                        <td>{{ substr($h->hora_fin, 0, 5) }}</td>
                        <td>
                            <a href="{{ route('admin.horarios.edit', $h->id_horario) }}" class="btn btn-sm" title="Editar" style="background:#e8f0fe;color:#1e3c72;border:none;border-radius:8px;padding:6px 10px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.horarios.destroy', $h->id_horario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este horario?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" title="Eliminar" style="background:#fce4ec;color:#c62828;border:none;border-radius:8px;padding:6px 10px;"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No hay horarios registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $horarios->links() }}
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
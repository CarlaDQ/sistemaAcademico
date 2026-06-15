@extends('layouts.app')

@section('title', 'Cursos - U.E. David Pinilla')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">Cursos</h3>
        <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Gesti&oacute;n de Cursos del sistema</p>
    </div>
    <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 20px;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Curso
    </a>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar nombre, grado o sección..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm" type="submit" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-weight:600;"><i class="bi bi-search me-1"></i> Filtrar</button>
                <a href="{{ route('admin.cursos.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm datatable">
                <thead style="background:#f4f6f9;">
                    <tr style="border-bottom:2px solid #1e3c72;">
                        <th>Nombre</th>
                        <th>Nivel</th>
                        <th>Grado</th>
                        <th>Sección</th>
                        <th>Año</th>
                        <th>Capacidad</th>
                        <th>Estudiantes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cursos as $c)
                    <tr>
                        <td>{{ $c->nombre }}</td>
                        <td>{{ $c->nivel->nombre ?? '—' }}</td>
                        <td>{{ $c->grado ?? '—' }}</td>
                        <td>{{ $c->seccion ?? '—' }}</td>
                        <td>{{ $c->anioLectivo->nombre ?? '—' }}</td>
                        <td>{{ $c->capacidad ?? '—' }}</td>
                        <td><span class="badge bg-{{ $c->estudiantes_count >= ($c->capacidad ?? 999) ? 'danger' : 'success' }}" style="border-radius:50px;padding:6px 12px;">{{ $c->estudiantes_count }}</span></td>
                        <td>
                            <a href="{{ route('admin.cursos.edit', $c->id_curso) }}" class="btn btn-sm" title="Editar" style="background:#e8f0fe;color:#1e3c72;border:none;border-radius:8px;padding:6px 10px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.cursos.destroy', $c->id_curso) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar este curso?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" title="Desactivar" style="background:#fce4ec;color:#c62828;border:none;border-radius:8px;padding:6px 10px;"><i class="bi bi-x-lg"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No hay cursos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $cursos->links() }}
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

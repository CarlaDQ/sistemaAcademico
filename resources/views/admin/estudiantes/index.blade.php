@extends('layouts.app')

@section('title', 'Estudiantes - U.E. David Pinilla')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">Estudiantes</h3>
        <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Gesti&oacute;n de Estudiantes del sistema</p>
    </div>
    <a href="{{ route('admin.estudiantes.create') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 20px;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Estudiante
    </a>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar nombre o código..." value="{{ request('search') }}">
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
                <a href="{{ route('admin.estudiantes.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm datatable">
                <thead style="background:#f4f6f9;">
                    <tr style="border-bottom:2px solid #1e3c72;">
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Curso</th>
                        <th>Código RUDE</th>
                        <th>Género</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estudiantes as $e)
                    <tr>
                        <td><code>{{ $e->codigo_estudiante }}</code></td>
                        <td>{{ $e->usuario->nombre ?? '—' }} {{ $e->usuario->apellido ?? '' }}</td>
                        <td>{{ $e->curso->nombre ?? '—' }}</td>
                        <td>
                            @if($e->codigo_rude)
                                <span class="badge bg-info text-dark">{{ $e->codigo_rude }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $e->genero ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.estudiantes.edit', $e->id_estudiante) }}" class="btn btn-sm" title="Editar" style="background:#e8f0fe;color:#1e3c72;border:none;border-radius:8px;padding:6px 10px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.estudiantes.destroy', $e->id_estudiante) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar este estudiante?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" title="Desactivar" style="background:#fce4ec;color:#c62828;border:none;border-radius:8px;padding:6px 10px;"><i class="bi bi-person-x"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay estudiantes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $estudiantes->links() }}
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

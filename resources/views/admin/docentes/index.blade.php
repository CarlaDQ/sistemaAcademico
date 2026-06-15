@extends('layouts.app')

@section('title', 'Docentes - U.E. David Pinilla')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">Docentes</h3>
        <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Gesti&oacute;n de Docentes del sistema</p>
    </div>
    <a href="{{ route('admin.docentes.create') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 20px;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Docente
    </a>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar nombre, código o especialidad..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm" type="submit" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-weight:600;"><i class="bi bi-search me-1"></i> Filtrar</button>
                <a href="{{ route('admin.docentes.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm datatable">
                <thead style="background:#f4f6f9;">
                    <tr style="border-bottom:2px solid #1e3c72;">
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Especialidad</th>
                        <th>Asignaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($docentes as $d)
                    <tr>
                        <td><code>{{ $d->codigo_docente }}</code></td>
                        <td>{{ $d->usuario->nombre ?? '—' }} {{ $d->usuario->apellido ?? '' }}</td>
                        <td>{{ $d->especialidad ?? '—' }}</td>
                        <td><span class="badge bg-secondary" style="border-radius:50px;padding:6px 12px;">{{ $d->asignaciones_count }}</span></td>
                        <td>
                            <a href="{{ route('admin.docentes.edit', $d->id_docente) }}" class="btn btn-sm" title="Editar" style="background:#e8f0fe;color:#1e3c72;border:none;border-radius:8px;padding:6px 10px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.docentes.destroy', $d->id_docente) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar este docente?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" title="Desactivar" style="background:#fce4ec;color:#c62828;border:none;border-radius:8px;padding:6px 10px;"><i class="bi bi-person-x"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay docentes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $docentes->links() }}
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

@extends('layouts.app')

@section('title', 'Padres de Familia - U.E. David Pinilla')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">Padres de Familia</h3>
        <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Gesti&oacute;n de Padres de Familia del sistema</p>
    </div>
    <a href="{{ route('admin.padres.create') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 20px;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Padre
    </a>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar nombre..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm" type="submit" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-weight:600;"><i class="bi bi-search me-1"></i> Filtrar</button>
                <a href="{{ route('admin.padres.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm datatable">
                <thead style="background:#f4f6f9;">
                    <tr style="border-bottom:2px solid #1e3c72;">
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Parentesco</th>
                        <th>Hijos vinculados</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($padres as $p)
                    <tr>
                        <td>{{ $p->usuario->nombre ?? '—' }} {{ $p->usuario->apellido ?? '' }}</td>
                        <td><small class="text-muted">{{ $p->usuario ? Crypt::decryptString($p->usuario->email_cifrado) : '—' }}</small></td>
                        <td>{{ $p->usuario->telefono ?? '—' }}</td>
                        <td>{{ $p->parentesco }}</td>
                        <td><span class="badge bg-secondary" style="border-radius:50px;padding:6px 12px;">{{ $p->hijos_count }}</span></td>
                        <td>
                            <a href="{{ route('admin.padres.edit', $p->id_padre) }}" class="btn btn-sm" title="Editar" style="background:#e8f0fe;color:#1e3c72;border:none;border-radius:8px;padding:6px 10px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.padres.destroy', $p->id_padre) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar este padre de familia?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" title="Desactivar" style="background:#fce4ec;color:#c62828;border:none;border-radius:8px;padding:6px 10px;"><i class="bi bi-person-x"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay padres de familia registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $padres->links() }}
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

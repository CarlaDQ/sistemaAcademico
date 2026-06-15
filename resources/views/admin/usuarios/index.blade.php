@extends('layouts.app')

@section('title', 'Usuarios - U.E. David Pinilla')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h3 style="color:#1e3c72;font-weight:700;margin-bottom:4px;">Usuarios</h3>
        <p style="color:#888;font-size:0.85rem;margin-bottom:0;">Gesti&oacute;n de Usuarios del sistema</p>
    </div>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);border:none;border-radius:10px;padding:8px 20px;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Usuario
    </a>
</div>

<div class="card" style="border-radius:14px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar nombre/CI/apellido..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="rol" id="filtro-rol" class="form-select form-select-sm">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $r)
                    <option value="{{ $r->id_rol }}" {{ request('rol') == $r->id_rol ? 'selected' : '' }}>{{ $r->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2" id="filtro-curso-wrapper" style="{{ request('rol') ? '' : 'display:none' }}">
                <select name="id_curso" class="form-select form-select-sm">
                    <option value="">Todos los cursos</option>
                    @foreach($cursos as $c)
                    <option value="{{ $c->id_curso }}" {{ request('id_curso') == $c->id_curso ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm" type="submit" style="background:linear-gradient(135deg,#1e3c72,#2a4a7f);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-weight:600;"><i class="bi bi-search me-1"></i> Filtrar</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm datatable">
                <thead style="background:#f4f6f9;">
                    <tr style="border-bottom:2px solid #1e3c72;">
                        <th>CI</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $u)
                    <tr>
                        <td><small>{{ $u->ci ?? '—' }}</small></td>
                        <td>{{ $u->nombre }} {{ $u->apellido }}</td>
                        <td><small>{{ $u->email_decrypted }}</small></td>
                        <td><span class="badge bg-info" style="border-radius:50px;padding:6px 12px;">{{ $u->rol->nombre ?? '—' }}</span></td>
                        <td>
                            <span class="badge bg-{{ $u->estado === 'activo' ? 'success' : 'danger' }}" style="border-radius:50px;padding:6px 12px;">
                                {{ $u->estado }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.usuarios.edit', $u->id_usuario) }}" class="btn btn-sm" title="Editar" style="background:#e8f0fe;color:#1e3c72;border:none;border-radius:8px;padding:6px 10px;">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.usuarios.destroy', $u->id_usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm" title="Desactivar" style="background:#fce4ec;color:#c62828;border:none;border-radius:8px;padding:6px 10px;"><i class="bi bi-person-x"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $usuarios->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.datatable').DataTable({
        paging: false,
        info: false,
        searching: false,
        language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json' }
    });

    $('#filtro-rol').on('change', function() {
        var val = $(this).find('option:selected').text().toLowerCase().trim();
        if (val === 'estudiante') {
            $('#filtro-curso-wrapper').show();
        } else {
            $('#filtro-curso-wrapper').hide();
            $('select[name="id_curso"]').val('');
        }
    });
});
</script>
@endpush

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'U.E. David Pinilla - Sistema Acad&eacute;mico')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; min-height: 100vh; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c0c0c0; border-radius: 3px; }

        .sidebar {
            width: 230px;
            min-height: 100vh;
            background: linear-gradient(180deg, #152a4f 0%, #1e3c72 50%, #2a4a7f 100%);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 18px 18px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .sidebar-brand .brand-text { font-size: 1rem; font-weight: 700; letter-spacing: 0.5px; }
        .sidebar-brand .brand-sub { font-size: 0.6rem; opacity: 0.6; letter-spacing: 1px; }

        .sidebar-nav { flex: 1; padding: 8px 0; overflow-y: auto; }
        .sidebar-nav .nav-item { padding: 0 8px; margin-bottom: 1px; }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
        }
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.18);
            box-shadow: inset 3px 0 0 #fff;
        }
        .sidebar-nav .nav-link i { font-size: 1rem; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 10px 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            padding: 6px 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .sidebar-footer .user-info:hover { background: rgba(255,255,255,0.1); }
        .sidebar-footer .user-avatar {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .sidebar-footer .user-name { font-size: 0.8rem; font-weight: 600; line-height: 1.2; }
        .sidebar-footer .user-role { font-size: 0.65rem; opacity: 0.6; }

        .main-content {
            margin-left: 230px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 230px);
        }
        .main-content .content-wrap { flex: 1; padding: 20px 28px; }
        .main-content .content-wrap .card { width: 100%; }
        .main-content .content-wrap .table-responsive { width: 100%; }
        .main-content .content-wrap .container-fluid { padding-left: 0; padding-right: 0; }
        .main-content .content-wrap .container-fluid.py-4 { padding-top: 0; padding-bottom: 0; }
        .main-content .page-footer {
            text-align: center;
            padding: 14px;
            color: #aaa;
            font-size: 0.8rem;
            border-top: 1px solid #e8ecf0;
            background: #fff;
        }

        .page-header {
            margin-bottom: 20px;
        }
        .page-header h3 {
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 2px;
            font-size: 1.3rem;
        }
        .page-header p {
            color: #888;
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        .topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 28px; background: #fff; border-bottom: 1px solid #e8ecf0;
        }
        .clock {
            font-size: 0.85rem; font-weight: 600; color: #1e3c72;
            font-family: 'Courier New', monospace; letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .sidebar { width: 56px; }
            .sidebar .brand-text, .sidebar .brand-sub,
            .sidebar .nav-link span, .sidebar .user-name, .sidebar .user-role { display: none; }
            .sidebar .sidebar-brand a { justify-content: center; }
            .sidebar .sidebar-footer .user-info { justify-content: center; }
            .main-content { margin-left: 56px; }
            .main-content .content-wrap { padding: 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        @if (session('user_id'))
        <nav class="sidebar">
            <div class="sidebar-brand">
                <a href="/">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:36px;height:36px;border-radius:8px;object-fit:cover;">
                    <div>
                        <div class="brand-text">U.E. David Pinilla</div>
                        <div class="brand-sub">UNIDAD EDUCATIVA</div>
                    </div>
                </a>
            </div>
            <ul class="sidebar-nav list-unstyled mb-0">
                @if (session('user_rol') === 'administrador')
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> <span>Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.estudiantes.index') }}" class="nav-link {{ request()->routeIs('admin.estudiantes.*') ? 'active' : '' }}">
                        <i class="bi bi-mortarboard"></i> <span>Estudiantes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.padres.index') }}" class="nav-link {{ request()->routeIs('admin.padres.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> <span>Padres de Familia</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.docentes.index') }}" class="nav-link {{ request()->routeIs('admin.docentes.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i> <span>Docentes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.cursos.index') }}" class="nav-link {{ request()->routeIs('admin.cursos.*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i> <span>Cursos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.materias.index') }}" class="nav-link {{ request()->routeIs('admin.materias.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> <span>Materias</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.asignaciones.index') }}" class="nav-link {{ request()->routeIs('admin.asignaciones.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i> <span>Asignaciones</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.horarios.index') }}" class="nav-link {{ request()->routeIs('admin.horarios.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-week"></i> <span>Horarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reportes.index') }}" class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-pdf"></i> <span>Reportes</span>
                    </a>
                </li>
                @endif
                @if (session('user_rol') === 'docente')
                <li class="nav-item">
                    <a href="{{ route('docente.dashboard') }}" class="nav-link {{ request()->routeIs('docente.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('docente.calificaciones.index') }}" class="nav-link {{ request()->routeIs('docente.calificaciones.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data"></i> <span>Calificaciones</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('docente.asistencia.index') }}" class="nav-link {{ request()->routeIs('docente.asistencia.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> <span>Asistencia</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('docente.citaciones.index') }}" class="nav-link {{ request()->routeIs('docente.citaciones.*') ? 'active' : '' }}">
                        <i class="bi bi-send"></i> <span>Citaciones/Avisos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('docente.reportes.index') }}" class="nav-link {{ request()->routeIs('docente.reportes.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> <span>Reportes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('docente.geocercas.index') }}" class="nav-link {{ request()->routeIs('docente.geocercas.*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt-fill"></i> <span>Geocercas</span>
                    </a>
                </li>
                @endif
                @if (session('user_rol') === 'estudiante')
                <li class="nav-item">
                    <a href="{{ route('estudiante.notas') }}" class="nav-link {{ request()->routeIs('estudiante.notas') ? 'active' : '' }}">
                        <i class="bi bi-book-fill"></i> <span>Mis Notas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('estudiante.asistencia') }}" class="nav-link {{ request()->routeIs('estudiante.asistencia') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> <span>Mi Asistencia</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('estudiante.citaciones') }}" class="nav-link {{ request()->routeIs('estudiante.citaciones') ? 'active' : '' }}">
                        <i class="bi bi-envelope"></i> <span>Citaciones</span>
                    </a>
                </li>
                @endif
                @if (session('user_rol') === 'padre_familia')
                <li class="nav-item">
                    <a href="{{ route('padre.hijos.index') }}" class="nav-link {{ request()->routeIs('padre.hijos.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> <span>Mis Hijos</span>
                    </a>
                </li>
                @endif
            </ul>
            <div class="sidebar-footer">
                <div class="dropdown">
                    <div class="user-info" data-bs-toggle="dropdown">
                        <div class="user-avatar"><i class="bi bi-person"></i></div>
                        <div>
                            <div class="user-name">{{ session('user_name', 'Usuario') }}</div>
                            <div class="user-role">{{ ucfirst(session('user_rol')) }}</div>
                        </div>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-dark shadow" style="border-radius:10px;border:none;">
                        <li><div class="dropdown-header">{{ session('user_name', 'Usuario') }}</div></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesion</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @endif

        <div class="main-content">
            <div class="topbar">
                <div></div>
                <div class="clock" id="reloj">--:--:--</div>
            </div>
            <div class="content-wrap">
                @include('partials.flash-messages')
                @yield('content')
            </div>
            <div class="page-footer">
                &copy; {{ date('Y') }} U.E. David Pinilla
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
    @stack('scripts')
    <script>
        function updateClock() {
            const now = new Date();
            const opts = { timeZone: 'America/La_Paz', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            document.getElementById('reloj').textContent = now.toLocaleTimeString('es-BO', opts);
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
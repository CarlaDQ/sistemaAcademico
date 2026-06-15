<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\EstudianteController as AdminEstudianteController;
use App\Http\Controllers\Admin\DocenteController as AdminDocenteController;
use App\Http\Controllers\Admin\CursoController;
use App\Http\Controllers\Admin\MateriaController;
use App\Http\Controllers\Admin\AsignacionController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Docente\CalificacionController;
use App\Http\Controllers\Docente\AsistenciaController;
use App\Http\Controllers\Docente\CitacionController;
use App\Http\Controllers\Docente\GeocercaController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\Padre\HijoController;

Route::get('/', function () {
    if (session('user_id')) {
        $rol = session('user_rol');
        $map = [
            'administrador' => route('admin.dashboard'),
            'docente' => route('docente.dashboard'),
            'estudiante' => route('estudiante.notas'),
            'padre_familia' => route('padre.hijos.index'),
        ];
        return redirect($map[$rol] ?? route('login'));
    }
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::any('/test-post', function () {
    return 'POST funcionó: ' . json_encode(request()->all());
});

Route::prefix('admin')->name('admin.')->middleware(['role:administrador'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('usuarios', UsuarioController::class)->parameters(['usuarios' => 'usuario']);
    Route::resource('estudiantes', AdminEstudianteController::class)->parameters(['estudiantes' => 'estudiante']);
    Route::resource('docentes', AdminDocenteController::class)->parameters(['docentes' => 'docente']);
    Route::resource('padres', App\Http\Controllers\Admin\PadreController::class)->parameters(['padres' => 'padre']);
    Route::get('padres/buscar/{ci}', [App\Http\Controllers\Admin\PadreController::class, 'buscarPorCi'])->name('padres.buscar');
    Route::get('padres/estudiantes/search', [App\Http\Controllers\Admin\PadreController::class, 'buscarEstudiantes'])->name('padres.estudiantes.search');
    Route::resource('cursos', CursoController::class)->parameters(['cursos' => 'curso']);
    Route::resource('materias', MateriaController::class)->parameters(['materias' => 'materia']);
    Route::resource('asignaciones', AsignacionController::class)->parameters(['asignaciones' => 'asignacione']);
    Route::resource('horarios', App\Http\Controllers\Admin\HorarioController::class)->parameters(['horarios' => 'horario']);
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/estudiantes', [ReporteController::class, 'estudiantes'])->name('estudiantes');
        Route::get('/calificaciones/{asignacion}', [ReporteController::class, 'calificaciones'])->name('calificaciones');
        Route::get('/asistencia/{asignacion}', [ReporteController::class, 'asistencia'])->name('asistencia');
        Route::get('/horario/{curso}', [ReporteController::class, 'horarioPdf'])->name('horario');
    });
});

Route::prefix('docente')->name('docente.')->middleware(['role:docente'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Docente\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calificaciones', [CalificacionController::class, 'index'])->name('calificaciones.index');
    Route::post('/calificaciones', [CalificacionController::class, 'store'])->name('calificaciones.store');
    Route::get('/calificaciones/actividades/{asignacion}', [CalificacionController::class, 'getActividades'])->name('calificaciones.actividades');
    Route::get('/asistencia', [AsistenciaController::class, 'index'])->name('asistencia.index');
    Route::post('/asistencia', [AsistenciaController::class, 'store'])->name('asistencia.store');
    Route::get('/citaciones', [CitacionController::class, 'index'])->name('citaciones.index');
    Route::post('/citaciones', [CitacionController::class, 'store'])->name('citaciones.store');
    Route::get('/citaciones/estudiantes/{curso}', [CitacionController::class, 'getEstudiantes'])->name('citaciones.estudiantes');
    Route::get('/geocercas', [GeocercaController::class, 'index'])->name('geocercas.index');
    Route::post('/geocercas', [GeocercaController::class, 'store'])->name('geocercas.store');
    Route::get('/geocercas/{geocerca}/edit', [GeocercaController::class, 'edit'])->name('geocercas.edit');
    Route::put('/geocercas/{geocerca}', [GeocercaController::class, 'update'])->name('geocercas.update');
    Route::delete('/geocercas/{geocerca}', [GeocercaController::class, 'destroy'])->name('geocercas.destroy');
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [App\Http\Controllers\Docente\ReporteController::class, 'index'])->name('index');
        Route::get('/estudiante/{estudiante}', [App\Http\Controllers\Docente\ReporteController::class, 'estudiante'])->name('estudiante');
        Route::get('/curso/{curso}', [App\Http\Controllers\Docente\ReporteController::class, 'curso'])->name('curso');
    });
});

Route::prefix('estudiante')->name('estudiante.')->middleware(['role:estudiante'])->group(function () {
    Route::get('/notas', [EstudianteController::class, 'notas'])->name('notas');
    Route::get('/asistencia', [EstudianteController::class, 'asistencia'])->name('asistencia');
    Route::get('/citaciones', [EstudianteController::class, 'citaciones'])->name('citaciones');
});

Route::prefix('padre')->name('padre.')->middleware(['role:padre_familia'])->group(function () {
    Route::get('/hijos', [HijoController::class, 'index'])->name('hijos.index');
    Route::get('/hijos/{estudiante}', [HijoController::class, 'show'])->name('hijos.show');
    Route::get('/permiso/crear/{estudiante}', [HijoController::class, 'permisoCrear'])->name('permiso.crear');
    Route::post('/permiso/guardar', [HijoController::class, 'permisoGuardar'])->name('permiso.guardar');
});

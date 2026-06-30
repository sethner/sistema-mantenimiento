<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\CargaTecnicoController;
use App\Http\Controllers\CategoriaComponenteController;
use App\Http\Controllers\ComponenteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\TipoEquipoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirección principal: la raíz dirige automáticamente al Dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Rutas protegidas bajo el middleware 'auth' (requieren inicio de sesión)
Route::middleware(['auth'])->group(function () {
    
    // Panel de control (Dashboard)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');

    // Gestión del perfil del usuario logueado
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // RUTAS EXCLUSIVAS DE ADMINISTRADORES
    // ==========================================
    Route::middleware('role:administrador')->group(function () {
        // Gestión CRUD de equipos y componentes
        Route::resource('equipos', EquipoController::class);
        Route::resource('componentes', ComponenteController::class);
        
        // Gestión de usuarios
        Route::resource('usuarios', UserController::class)->except(['show']);
        Route::get('usuarios/{usuario}', [UserController::class, 'show'])->name('usuarios.show');

        // Vinculación y desvinculación de componentes sobre equipos individuales
        Route::post('equipos/{equipo}/componentes', [EquipoController::class, 'agregarComponente'])
            ->name('equipos.componentes.agregar');
        Route::delete('equipos/{equipo}/componentes/{componente}', [EquipoController::class, 'quitarComponente'])
            ->name('equipos.componentes.quitar');
        Route::put('equipos/{equipo}/componentes/{componente}', [EquipoController::class, 'cambiarEstado'])
            ->name('equipos.componentes.estado');

        // Categorías de componentes y tipos de equipos (Laptop, Servidores, etc.)
        Route::resource('categorias', CategoriaComponenteController::class)->except(['show']);
        Route::resource('tipos-equipos', TipoEquipoController::class)->except(['show']);

        // Ajustes del sistema (Datos de cabecera de la Institución)
        Route::get('configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::put('configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'update'])->name('configuracion.update');

        // Descarga de reportes ejecutivos del Dashboard en PDF
        Route::get('reportes/dashboard', [\App\Http\Controllers\ReporteController::class, 'descargarDashboard'])
            ->name('reportes.dashboard')
            ->middleware('role:administrador,supervisor');
    });

    // ==========================================
    // RUTAS DE REPORTES (Admin, Técnico, Supervisor)
    // ==========================================
    Route::middleware('role:administrador,tecnico,supervisor')->group(function () {
        Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/bienes/pdf', [ReporteController::class, 'descargarFichaBienes'])->name('reportes.bienes.pdf');
        Route::get('reportes/tecnico/pdf', [ReporteController::class, 'descargarReporteTecnico'])->name('reportes.tecnico.pdf');
        Route::get('reportes/mantenimientos/pdf', [ReporteController::class, 'descargarReporteMantenimientos'])->name('reportes.mantenimientos.pdf');
        Route::get('reportes/baja/pdf', [ReporteController::class, 'descargarReporteBaja'])->name('reportes.baja.pdf');
        Route::get('reportes/inversion/pdf', [ReporteController::class, 'descargarReporteInversion'])->name('reportes.inversion.pdf');
    });

    // ==========================================
    // RUTAS OPERATIVAS (Admin, Técnico)
    // ==========================================
    Route::middleware('role:administrador,tecnico')->group(function () {
        // Gestión CRUD y control de órdenes de mantenimiento
        Route::resource('mantenimientos', MantenimientoController::class);
        Route::patch('mantenimientos/{mantenimiento}/iniciar', [MantenimientoController::class, 'iniciar'])->name('mantenimientos.iniciar');
        Route::delete('mantenimientos/fotos/{foto}', [MantenimientoController::class, 'eliminarFoto'])->name('mantenimientos.fotos.eliminar');

        // Endpoints de Inteligencia Artificial (groq api)
        Route::post('ai/diagnosticar', [AiController::class, 'suggestDiagnosis'])->name('ai.diagnosticar');
        Route::post('ai/analisis-predictivo/{equipo}', [AiController::class, 'predictiveAnalysis'])->name('ai.predictivo');

        // Módulo de Notificaciones y alertas del sistema
        Route::get('notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::get('notificaciones/conteo', [NotificacionController::class, 'conteo'])->name('notificaciones.conteo');
        Route::get('notificaciones/recientes', [NotificacionController::class, 'recientes'])->name('notificaciones.recientes');
        Route::post('notificaciones/limpiar', [NotificacionController::class, 'limpiar'])->name('notificaciones.limpiar');
        Route::post('notificaciones/{notificacion}/leida', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leida');
        Route::post('notificaciones/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.marcar-todas');

        // Mapeo de Carga de Trabajo de técnicos (restringido adicionalmente solo a Administradores)
        Route::get('carga-tecnico', [CargaTecnicoController::class, 'index'])
            ->name('carga-tecnico.index')
            ->middleware('role:administrador');
    });
});

// Incluir archivos de rutas de autenticación nativas de Laravel (Breeze/Jetstream)
require __DIR__.'/auth.php';




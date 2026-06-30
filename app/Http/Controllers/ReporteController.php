<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use App\Models\Configuracion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Clase ReporteController
 * Controla la generación y descarga en formato PDF de reportes técnicos, fichas de inventario, equipos dados de baja e inversión financiera.
 */
class ReporteController extends Controller
{
    /**
     * Obtiene los datos generales de configuración institucional para renderizar las cabeceras de los PDFs.
     */
    private function getConfig()
    {
        return Configuracion::first() ?? new Configuracion([
            'nombre_institucion' => 'SISTEMA DE MANTENIMIENTO AIP',
            'director_nombre' => 'DIRECTOR GENERAL'
        ]);
    }

    /**
     * Muestra la pantalla general de Reportes con métricas agrupadas.
     * Si el usuario logueado es técnico, solo se cargan datos asociados a sus tareas.
     */
    public function index()
    {
        $mantenimientosQuery = Mantenimiento::with('equipo', 'usuario')
            ->whereHas('equipo', function ($q) {
                $q->where('estado', '!=', 'dado_de_baja');
            });
        $equiposQuery = Equipo::withCount('mantenimientos')->with('tipo')
            ->where('estado', '!=', 'dado_de_baja');

        // Restringir consulta si el perfil es técnico
        if (auth()->user()->hasRole('tecnico')) {
            $mantenimientosQuery->where('user_id', auth()->id());
            $equiposQuery->whereHas('mantenimientos', function($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $mantenimientos = $mantenimientosQuery->latest('fecha')->get();
        $equipos = $equiposQuery->get();

        // Agrupar cantidad de mantenimientos por mes
        $mantenimientosPorMes = $mantenimientos
            ->groupBy(fn ($mantenimiento) => optional($mantenimiento->fecha)->format('Y-m') ?? 'Sin fecha')
            ->map->count()
            ->sortKeysDesc()
            ->take(6);

        // Agrupar cantidad de equipos por estado actual
        $porEstadoEquipo = $equipos
            ->groupBy('estado')
            ->map->count();

        // Agrupar cantidad por tipo de mantenimiento
        $porTipoMantenimiento = $mantenimientos
            ->groupBy(fn ($mantenimiento) => $mantenimiento->tipo ?? 'correctivo')
            ->map->count();

        // Equipos en estado crítico (estado con falla o más de 3 mantenimientos)
        $equiposCriticos = $equipos
            ->filter(fn ($equipo) => $equipo->mantenimientos_count >= 3 || $equipo->estado === 'con_falla')
            ->sortByDesc('mantenimientos_count')
            ->values();

        $tecnicosQuery = User::whereHas('roles', function ($q) {
            $q->where('nombre', 'tecnico');
        });

        if (auth()->user()->hasRole('tecnico')) {
            $tecnicosQuery->where('id', auth()->id());
        }

        $tecnicos = $tecnicosQuery->get();

        return view('reportes.index', [
            'equipos' => $equipos,
            'mantenimientos' => $mantenimientos,
            'mantenimientosPorMes' => $mantenimientosPorMes,
            'porEstadoEquipo' => $porEstadoEquipo,
            'porTipoMantenimiento' => $porTipoMantenimiento,
            'equiposCriticos' => $equiposCriticos,
            'tecnicos' => $tecnicos,
            'config' => $this->getConfig()
        ]);
    }

    /**
     * Descarga la Ficha de Bienes de Inventario en formato PDF.
     */
    public function descargarFichaBienes(Request $request)
    {
        $query = Equipo::with(['tipo', 'componentes'])->where('estado', '!=', 'dado_de_baja');

        if ($request->filled('equipo_id')) {
            $query->where('id', $request->equipo_id);
        }

        $equipos = $query->get();
        // Agrupar los bienes por tipo para mostrarlos de forma ordenada en el PDF
        $equiposAgrupados = $equipos->groupBy(function($equipo) {
            return $equipo->tipo ? $equipo->tipo->nombre : 'OTROS';
        });

        $pdf = Pdf::loadView('reportes.pdf_bienes', [
            'equiposAgrupados' => $equiposAgrupados,
            'config' => $this->getConfig()
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Ficha_de_Bienes.pdf');
    }

    /**
     * Descarga el reporte detallado del desempeño e historial de actividades de un técnico específico.
     */
    public function descargarReporteTecnico(Request $request)
    {
        $query = Mantenimiento::with(['equipo', 'usuario'])
            ->whereHas('equipo', function ($q) {
                $q->where('estado', '!=', 'dado_de_baja');
            })
            ->orderBy('fecha', 'desc');
        
        $tecnicoSeleccionado = null;
        
        // Filtros de fecha de inicio y fin
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->fecha_fin);
        }

        // Si es técnico, forzar su filtro. Si es administrador, filtrar opcionalmente por técnico_id
        if (auth()->user()->hasRole('tecnico')) {
            $query->where('user_id', auth()->id());
            $tecnicoSeleccionado = auth()->user();
        } elseif ($request->filled('tecnico_id')) {
            $query->where('user_id', $request->tecnico_id);
            $tecnicoSeleccionado = User::find($request->tecnico_id);
        } else {
            $query->whereHas('usuario.roles', function ($q) {
                $q->where('nombre', 'tecnico');
            });
        }

        $mantenimientos = $query->get();

        $estadisticas = [
            'total' => $mantenimientos->count(),
            'finalizados' => $mantenimientos->where('estado', 'finalizado')->count(),
            'pendientes' => $mantenimientos->whereIn('estado', ['pendiente', 'en_proceso'])->count(),
            'preventivos' => $mantenimientos->where('tipo', 'preventivo')->count(),
            'correctivos' => $mantenimientos->where('tipo', 'correctivo')->count(),
            'costo_total' => $mantenimientos->sum('costo'),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ];

        $pdf = Pdf::loadView('reportes.pdf_tecnico', [
            'mantenimientos' => $mantenimientos,
            'estadisticas' => $estadisticas,
            'tecnicoSeleccionado' => $tecnicoSeleccionado,
            'config' => $this->getConfig()
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Reporte_Mantenimientos_Tecnicos.pdf');
    }

    /**
     * Descarga el listado completo y detallado del historial de mantenimientos en PDF.
     */
    public function descargarReporteMantenimientos(Request $request)
    {
        $query = Mantenimiento::with(['equipo.tipo', 'usuario'])
            ->whereHas('equipo', function ($q) {
                $q->where('estado', '!=', 'dado_de_baja');
            })
            ->orderBy('fecha', 'desc');
        
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->fecha_fin);
        }

        if (auth()->user()->hasRole('tecnico')) {
            $query->where('user_id', auth()->id());
        }

        $equipoSeleccionado = null;
        if ($request->filled('equipo_id')) {
            $query->where('equipo_id', $request->equipo_id);
            $equipoSeleccionado = Equipo::with('tipo')->find($request->equipo_id);
        }

        $mantenimientos = $query->get();

        $estadisticas = [
            'total' => $mantenimientos->count(),
            'preventivos' => $mantenimientos->where('tipo', 'preventivo')->count(),
            'correctivos' => $mantenimientos->where('tipo', 'correctivo')->count(),
            'costo_total' => $mantenimientos->sum('costo'),
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ];

        $pdf = Pdf::loadView('reportes.pdf_mantenimientos', [
            'mantenimientos' => $mantenimientos,
            'estadisticas' => $estadisticas,
            'equipoSeleccionado' => $equipoSeleccionado,
            'config' => $this->getConfig()
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Historial_Mantenimientos.pdf');
    }

    /**
     * Genera y descarga un Reporte Ejecutivo global del Dashboard con un consolidado general del estado operativo de los equipos.
     */
    public function descargarDashboard()
    {
        $equiposQuery = Equipo::with('tipo')->where('estado', '!=', 'dado_de_baja');
        $mantenimientosQuery = Mantenimiento::with('equipo.tipo')
            ->whereHas('equipo', function ($q) {
                $q->where('estado', '!=', 'dado_de_baja');
            });

        if (auth()->user()->hasRole('tecnico')) {
            $mantenimientosQuery->where('user_id', auth()->id());
            $equiposQuery->whereHas('mantenimientos', fn($q) => $q->where('user_id', auth()->id()));
        }

        $equipos = $equiposQuery->get();
        $mantenimientos = $mantenimientosQuery->get();
        $tecnicosQuery = User::whereHas('roles', fn($q) => $q->where('nombre', 'tecnico'));

        if (auth()->user()->hasRole('tecnico')) {
            $tecnicosQuery->where('id', auth()->id());
        }

        $tecnicos = $tecnicosQuery->get();

        // Procesar distribución de equipos e inversión monetaria por categoría/tipo
        $equiposPorTipo = [];
        foreach ($equipos as $e) {
            $tipo = $e->tipo->nombre ?? 'OTRO';
            if (!isset($equiposPorTipo[$tipo])) {
                $equiposPorTipo[$tipo] = ['cantidad' => 0, 'costo' => 0];
            }
            $equiposPorTipo[$tipo]['cantidad']++;
        }
        foreach ($mantenimientos as $m) {
            if ($m->equipo) {
                $tipo = $m->equipo->tipo->nombre ?? 'OTRO';
                if (!isset($equiposPorTipo[$tipo])) {
                    $equiposPorTipo[$tipo] = ['cantidad' => 0, 'costo' => 0];
                }
                $equiposPorTipo[$tipo]['costo'] += $m->costo;
            }
        }
        $equiposCriticos = $equipos->where('estado', 'con_falla');

        $data = [
            'totalEquipos' => $equipos->count(),
            'equiposOperativos' => $equipos->where('estado', 'operativo')->count(),
            'equiposFalla' => $equipos->where('estado', 'con_falla')->count(),
            'equiposBaja' => $equipos->where('estado', 'dado_de_baja')->count(),
            'totalMantenimientos' => $mantenimientos->count(),
            'mantenimientosMes' => $mantenimientos->where('fecha', '>=', now()->startOfMonth())->count(),
            'tecnicos' => $tecnicos->count(),
            'totalCosto' => $mantenimientos->sum('costo'),
            'equiposPorTipo' => $equiposPorTipo,
            'equiposCriticos' => $equiposCriticos,
            'config' => $this->getConfig()
        ];

        $pdf = Pdf::loadView('reportes.pdf_dashboard', $data)->setPaper('a4', 'portrait');
        return $pdf->download('Resumen_Ejecutivo_AIP.pdf');
    }

    /**
     * Genera un reporte consolidado de todos los equipos y activos tecnológicos que han sido dados de baja del sistema.
     */
    public function descargarReporteBaja()
    {
        $equipos = Equipo::with(['tipo', 'componentes.categoria'])->where('estado', 'dado_de_baja')->get();

        $pdf = Pdf::loadView('reportes.pdf_baja', [
            'equipos' => $equipos,
            'config' => $this->getConfig()
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Reporte_Equipos_Baja.pdf');
    }

    /**
     * Genera un desglose e informe financiero sobre los costos de inversión asociados a mantenimientos en un año o mes específico.
     */
    public function descargarReporteInversion(Request $request)
    {
        $request->validate([
            'anio' => 'required|numeric|min:2020',
            'mes' => 'nullable|numeric|min:1|max:12'
        ]);

        $query = Mantenimiento::with(['equipo.tipo', 'usuario'])
                    ->whereHas('equipo', function ($q) {
                        $q->where('estado', '!=', 'dado_de_baja');
                    })
                    ->whereYear('fecha', $request->anio);

        if ($request->filled('mes')) {
            $query->whereMonth('fecha', $request->mes);
        }

        $mantenimientos = $query->orderBy('fecha', 'desc')->get();

        $estadisticas = [
            'total_mantenimientos' => $mantenimientos->count(),
            'costo_total' => $mantenimientos->sum('costo'),
            'preventivos_count' => $mantenimientos->where('tipo', 'preventivo')->count(),
            'correctivos_count' => $mantenimientos->where('tipo', 'correctivo')->count(),
            'costo_preventivos' => $mantenimientos->where('tipo', 'preventivo')->sum('costo'),
            'costo_correctivos' => $mantenimientos->where('tipo', 'correctivo')->sum('costo'),
            'anio' => $request->anio,
            'mes' => $request->mes,
        ];

        $pdf = Pdf::loadView('reportes.pdf_inversion', [
            'mantenimientos' => $mantenimientos,
            'estadisticas' => $estadisticas,
            'config' => $this->getConfig()
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Reporte_Inversion_' . $request->anio . ($request->filled('mes') ? '_' . str_pad($request->mes, 2, '0', STR_PAD_LEFT) : '') . '.pdf');
    }
}


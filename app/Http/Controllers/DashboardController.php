<?php

namespace App\Http\Controllers;

use App\Models\Componente;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use Carbon\Carbon;

/**
 * Clase DashboardController
 * Centraliza la visualización de estadísticas principales para los perfiles de Administrador y Técnico.
 */
class DashboardController extends Controller
{
    /**
     * Redirecciona al usuario al Dashboard correspondiente según su Rol asignado.
     */
    public function __invoke(\Illuminate\Http\Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user && $user->hasRole('administrador')) {
            return $this->dashboardAdministrador();
        } elseif ($user && $user->hasRole('tecnico')) {
            return $this->dashboardTecnico($user);
        } elseif ($user && $user->hasRole('supervisor')) {
            return redirect()->route('reportes.index');
        }

        abort(403, 'No tienes un rol asignado para acceder al dashboard.');
    }

    /**
     * Genera la vista del Dashboard para administradores con KPIs globales y costos.
     */
    private function dashboardAdministrador()
    {
        $hoy = Carbon::today();

        // Obtener equipos con el recuento de sus mantenimientos para identificar equipos críticos
        $equipos = Equipo::withCount('mantenimientos')->get();
        $mantenimientos = Mantenimiento::with('equipo', 'usuario')->latest('fecha')->get();

        // Obtener mantenimientos preventivos pendientes que ya han vencido
        $preventivosVencidos = Mantenimiento::with('equipo', 'usuario')
            ->where('tipo', 'preventivo')
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->whereDate('fecha', '<', $hoy)
            ->orderBy('fecha')
            ->take(5)
            ->get();

        // Obtener mantenimientos programados para los siguientes 7 días
        $preventivosProximos = Mantenimiento::with('equipo', 'usuario')
            ->where('tipo', 'preventivo')
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->whereBetween('fecha', [$hoy, $hoy->copy()->addDays(7)])
            ->orderBy('fecha')
            ->get();

        // Equipos críticos: aquellos con fallas activas o con 3 o más mantenimientos registrados
        $equiposCriticos = $equipos
            ->filter(fn ($equipo) => $equipo->mantenimientos_count >= 3 || $equipo->estado === 'con_falla')
            ->sortByDesc('mantenimientos_count')
            ->take(5)
            ->values();

        // Calcular la inversión monetaria acumulada en mantenimientos (Anual y Mensual)
        $inversionAnual = Mantenimiento::whereYear('fecha', Carbon::now()->year)
            ->sum('costo');

        $inversionMensual = Mantenimiento::whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->sum('costo');

        return view('dashboard.admin', [
            'totalEquipos' => $equipos->count(),
            'equiposOperativos' => $equipos->where('estado', 'operativo')->count(),
            'equiposEnMantenimiento' => $equipos->where('estado', 'en_mantenimiento')->count(),
            'equiposConFalla' => $equipos->where('estado', 'con_falla')->count(),
            'equiposBaja' => $equipos->where('estado', 'dado_de_baja')->count(),
            'componentes' => Componente::count(),
            'mantenimientosPendientes' => $mantenimientos->where('estado', 'pendiente')->count(),
            'mantenimientosProceso' => $mantenimientos->where('estado', 'en_proceso')->count(),
            'mantenimientosFinalizados' => $mantenimientos->where('estado', 'finalizado')->count(),
            'preventivosVencidos' => $preventivosVencidos,
            'preventivosProximos' => $preventivosProximos,
            'equiposCriticos' => $equiposCriticos,
            'inversionAnual' => $inversionAnual,
            'inversionMensual' => $inversionMensual,
            'ultimosMantenimientos' => $mantenimientos->take(6),
        ]);
    }

    /**
     * Genera la vista del Dashboard para técnicos, enfocándose en sus tareas asignadas.
     */
    private function dashboardTecnico(\App\Models\User $user)
    {
        $hoy = Carbon::today();

        // Obtener órdenes de mantenimiento asignadas al técnico autenticado
        $misMantenimientos = Mantenimiento::with('equipo')
            ->where('user_id', $user->id)
            ->latest('fecha')
            ->get();

        $tareasPendientes = $misMantenimientos->where('estado', 'pendiente')->count();
        $tareasEnProceso = $misMantenimientos->where('estado', 'en_proceso')->count();
        $tareasCompletadas = $misMantenimientos->where('estado', 'finalizado')->count();
        
        // Mantenimientos vencidos del técnico actual
        $mantenimientosVencidos = Mantenimiento::with('equipo')
            ->where('user_id', $user->id)
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->whereDate('fecha', '<', $hoy)
            ->orderBy('fecha')
            ->get();

        // Próximas órdenes pendientes o en proceso
        $proximosMantenimientos = Mantenimiento::with('equipo')
            ->where('user_id', $user->id)
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->whereDate('fecha', '>=', $hoy)
            ->orderBy('fecha')
            ->take(10)
            ->get();

        $ultimosCompletados = $misMantenimientos->where('estado', 'finalizado')->take(5);

        return view('dashboard.tecnico', compact(
            'tareasPendientes',
            'tareasEnProceso',
            'tareasCompletadas',
            'mantenimientosVencidos',
            'proximosMantenimientos',
            'ultimosCompletados'
        ));
    }

    /**
     * Retorna datos formateados en formato JSON para pintar gráficos estadísticos interactivos en el Dashboard (ChartJS).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardData(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('administrador')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $queryMantenimientos = Mantenimiento::with('equipo', 'usuario');

        // Aplicar filtros de fecha y estado si el usuario los especifica en el panel
        if ($request->filled('fecha_inicio')) {
            $queryMantenimientos->whereDate('fecha', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $queryMantenimientos->whereDate('fecha', '<=', $request->fecha_fin);
        }
        if ($request->filled('tipo')) {
            $queryMantenimientos->where('tipo', $request->tipo);
        }
        if ($request->filled('estado')) {
            $queryMantenimientos->where('estado', $request->estado);
        }

        $mantenimientos = $queryMantenimientos->latest('fecha')->get();
        $equipos = Equipo::withCount('mantenimientos')->get();

        // --- DATOS PARA GRÁFICOS ---
        // 1. Mantenimientos agrupados por mes
        $mantenimientosPorMes = $mantenimientos->groupBy(function($m) {
            return $m->fecha ? $m->fecha->translatedFormat('M Y') : 'Sin Fecha';
        })->map->count();

        // 2. Costo acumulado de inversión agrupado por mes
        $inversionPorMes = $mantenimientos->groupBy(function($m) {
            return $m->fecha ? $m->fecha->translatedFormat('M Y') : 'Sin Fecha';
        })->map(function($group) {
            return $group->sum('costo');
        });

        // 3. Distribución global de estados de equipos
        $estadoEquiposGlobal = $equipos->groupBy('estado')->map->count();
        $desglosePorTipo = $equipos->groupBy('estado')->map(function($items) {
            return $items->groupBy('tipo.nombre')->map->count();
        });

        // 4. Mantenimientos por tipo (Correctivos vs Preventivos)
        $mantenimientosPorTipo = $mantenimientos->groupBy('tipo')->map->count();

        // --- KPIs ---
        $isFiltered = $request->filled('fecha_inicio') || $request->filled('fecha_fin') || $request->filled('tipo') || $request->filled('estado');

        $inversionAnual = $isFiltered
            ? $mantenimientos->sum('costo')
            : $mantenimientos->filter(fn($m) => $m->fecha && $m->fecha->year == Carbon::now()->year)->sum('costo');

        $inversionMensual = $isFiltered
            ? ($mantenimientos->count() > 0 ? $mantenimientos->sum('costo') / max(1, $mantenimientos->groupBy(fn($m) => $m->fecha ? $m->fecha->format('Y-m') : '')->count()) : 0)
            : $mantenimientos->filter(fn($m) => $m->fecha && $m->fecha->isCurrentMonth() && $m->fecha->isCurrentYear())->sum('costo');

        $kpis = [
            'totalEquipos' => $equipos->count(),
            'equiposOperativos' => $equipos->where('estado', 'operativo')->count(),
            'equiposConFalla' => $equipos->where('estado', 'con_falla')->count(),
            'mantenimientosPendientes' => $mantenimientos->where('estado', 'pendiente')->count(),
            'inversionAnual' => $inversionAnual,
            'inversionMensual' => $inversionMensual,
            'isFiltered' => $isFiltered
        ];

        return response()->json([
            'kpis' => $kpis,
            'charts' => [
                'mantenimientosPorMes' => [
                    'labels' => $mantenimientosPorMes->keys(),
                    'data' => $mantenimientosPorMes->values()
                ],
                'inversionPorMes' => [
                    'labels' => $inversionPorMes->keys(),
                    'data' => $inversionPorMes->values()
                ],
                'estadoEquipos' => [
                    'labels' => $estadoEquiposGlobal->keys(),
                    'data' => $estadoEquiposGlobal->values(),
                    'desglose' => $desglosePorTipo
                ],
                'mantenimientosPorTipo' => [
                    'labels' => $mantenimientosPorTipo->keys(),
                    'data' => $mantenimientosPorTipo->values()
                ]
            ]
        ]);
    }
}

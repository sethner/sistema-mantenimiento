<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Dashboard General</h2>
                <p class="text-sm text-gray-500">Panel de control y análisis de rendimiento</p>
            </div>
            </div>
    </x-slot>

    @php
        $user = auth()->user();
        $roleName = optional($user->roles->first())->nombre ?? 'Usuario';
    @endphp

    <style>
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .animate-bounce-slow {
            animation: bounce-slow 3s ease-in-out infinite;
        }
    </style>

    <!-- BIENVENIDA PREMIUM -->
    <div class="relative bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 rounded-3xl overflow-hidden shadow-xl shadow-indigo-200/50 p-6 sm:p-8 mb-8 border border-indigo-500/10">
        <!-- Decoraciones de fondo -->
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-60 h-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 -mb-10 w-48 h-48 rounded-full bg-indigo-400/20 blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Avatar o Icono llamativo -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 shadow-inner shrink-0 animate-bounce-slow">
                    <span class="text-3xl sm:text-4xl">👋</span>
                </div>
                <div>
                    <!-- Rol llamativo con badge premium -->
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white border border-white/30 tracking-wider uppercase mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                        Sesión como: {{ ucfirst($roleName) }}
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        ¡Hola, {{ $user->name }}!
                    </h2>
                    <p class="text-sm sm:text-base text-indigo-100 mt-1 max-w-xl font-medium">
                        Bienvenido de nuevo al panel de control del Sistema de Mantenimiento. Tienes acceso completo para gestionar y supervisar las operaciones tecnológicas.
                    </p>
                </div>
            </div>
            
            <div class="bg-white/10 backdrop-blur-md px-5 py-4 rounded-2xl border border-white/20 flex flex-col items-center justify-center min-w-[150px] shrink-0 text-white self-stretch md:self-auto">
                <span class="text-xs font-semibold text-indigo-200 uppercase tracking-wider">Fecha de hoy</span>
                <span class="text-2xl font-black mt-1">{{ now()->format('d') }}</span>
                <span class="text-xs font-bold text-indigo-100 mt-1 uppercase">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('MMMM, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" class="w-full rounded-xl border-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="w-full rounded-xl border-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tipo de Mantenimiento</label>
                <select name="tipo" id="tipo" class="w-full rounded-xl border-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-600">
                    <option value="">Todos</option>
                    <option value="preventivo">Preventivo</option>
                    <option value="correctivo">Correctivo</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Estado</label>
                <select name="estado" id="estado" class="w-full rounded-xl border-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-600">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="finalizado">Finalizado</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-xl shadow-md hover:bg-gray-800 transition">
                    <x-heroicon-o-funnel class="w-5 h-5" />
                    Filtrar
                </button>
                <button type="button" id="btnResetFilters" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition" title="Limpiar Filtros">
                    <x-heroicon-o-arrow-path class="w-5 h-5" />
                </button>
            </div>
        </form>
    </div>

    <!-- KPIs Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
        <!-- KPI 1 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                <x-heroicon-s-computer-desktop class="w-20 h-20 text-indigo-600" />
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500">Total Equipos</p>
                <p class="text-3xl font-bold text-gray-900 mt-2" id="kpi-totalEquipos">{{ $totalEquipos }}</p>
                <div class="mt-2 flex items-center text-xs text-indigo-600 font-medium">
                    <x-heroicon-s-chart-bar-square class="w-4 h-4 mr-1" />
                    <span>Inventario Global</span>
                </div>
            </div>
        </div>

        <!-- KPI 2 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                <x-heroicon-s-check-badge class="w-20 h-20 text-emerald-500" />
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500">Operativos</p>
                <p class="text-3xl font-bold text-gray-900 mt-2" id="kpi-equiposOperativos">{{ $equiposOperativos }}</p>
                <div class="mt-2 flex items-center text-xs text-emerald-600 font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-2 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    <span>Condición Óptima</span>
                </div>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                <x-heroicon-s-exclamation-triangle class="w-20 h-20 text-rose-500" />
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500">Con Falla</p>
                <p class="text-3xl font-bold text-gray-900 mt-2" id="kpi-equiposConFalla">{{ $equiposConFalla }}</p>
                <div class="mt-2 flex items-center text-xs text-rose-600 font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-rose-500 mr-2 shadow-[0_0_8px_rgba(244,63,94,0.5)]"></span>
                    <span>Requieren Atención</span>
                </div>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                <x-heroicon-s-clock class="w-20 h-20 text-amber-500" />
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500">Mants. Pendientes</p>
                <p class="text-3xl font-bold text-gray-900 mt-2" id="kpi-mantenimientosPendientes">{{ $mantenimientosPendientes }}</p>
                <div class="mt-2 flex items-center text-xs text-amber-600 font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-2 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                    <span>En cola de trabajo</span>
                </div>
            </div>
        </div>

        <!-- KPI 5: Inversión Anual -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                <x-heroicon-s-banknotes class="w-20 h-20 text-indigo-600" />
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500" id="label-inversionAnual">Inversión del Año</p>
                <p class="text-xl font-black text-gray-900 mt-2">S/ <span id="kpi-inversionAnual">{{ number_format($inversionAnual, 2) }}</span></p>
                <div class="mt-2 flex items-center text-xs text-indigo-600 font-medium">
                    <x-heroicon-s-calendar class="w-4 h-4 mr-1" />
                    <span>Año actual</span>
                </div>
            </div>
        </div>

        <!-- KPI 6: Inversión Mensual -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                <x-heroicon-s-calendar-days class="w-20 h-20 text-emerald-600" />
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500" id="label-inversionMensual">Inversión del Mes</p>
                <p class="text-xl font-black text-gray-900 mt-2">S/ <span id="kpi-inversionMensual">{{ number_format($inversionMensual, 2) }}</span></p>
                <div class="mt-2 flex items-center text-xs text-emerald-600 font-medium">
                    <x-heroicon-s-arrow-trending-up class="w-4 h-4 mr-1" />
                    <span>Mes actual</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart 1: Line (Tendencia Mantenimientos) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2 relative flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-bold text-gray-800">Mantenimientos por Mes</h3>
                    <p class="text-xs text-gray-500">Evolución temporal de trabajos realizados</p>
                </div>
                <div class="p-2 bg-indigo-50 rounded-xl text-indigo-600 shadow-inner">
                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                </div>
            </div>
            <div class="relative flex-grow w-full" style="min-height: 280px;">
                <canvas id="chartMantenimientosMes"></canvas>
            </div>
        </div>

        <!-- Chart 2: Doughnut (Estado Equipos Detallado) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-bold text-gray-800">Estado de Equipos</h3>
                    <p class="text-xs text-gray-500">Distribución global por condición</p>
                </div>
                <div class="p-2 bg-blue-50 rounded-xl text-blue-600 shadow-inner">
                    <x-heroicon-o-chart-pie class="w-5 h-5" />
                </div>
            </div>
            <div class="relative flex-grow w-full flex justify-center items-center" style="min-height: 280px;">
                <canvas id="chartEstadoEquipos"></canvas>
            </div>
        </div>
    </div>

    <!-- Row 3: Bar Chart & Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Chart 3: Line (Inversión por Mes) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2 relative flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-bold text-gray-800">Inversión por Mes</h3>
                    <p class="text-xs text-gray-500">Costo total de materiales y servicios por mes</p>
                </div>
                <div class="p-2 bg-emerald-50 rounded-xl text-emerald-600 shadow-inner">
                    <x-heroicon-o-currency-dollar class="w-5 h-5" />
                </div>
            </div>
            <div class="relative flex-grow w-full" style="min-height: 240px;">
                <canvas id="chartInversionMes"></canvas>
            </div>
        </div>

        <!-- Chart 4: Bar (Fallas/Tipos) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-bold text-gray-800">Trabajos por Tipo</h3>
                    <p class="text-xs text-gray-500">Preventivos vs Correctivos</p>
                </div>
                <div class="p-2 bg-sky-50 rounded-xl text-sky-600 shadow-inner">
                    <x-heroicon-o-square-3-stack-3d class="w-5 h-5" />
                </div>
            </div>
            <div class="relative flex-grow w-full flex justify-center items-center" style="min-height: 240px;">
                <canvas id="chartMantenimientosTipo"></canvas>
            </div>
        </div>
    </div>

    <!-- Row 4: Alertas y Equipos Críticos (Lado a Lado) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Preventivos Vencidos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="bg-rose-50 px-5 py-4 border-b border-rose-100 flex items-center gap-2">
                <x-heroicon-s-exclamation-circle class="w-5 h-5 text-rose-600" />
                <h3 class="font-bold text-rose-800 text-sm">Preventivos Vencidos (Top 5)</h3>
            </div>
            <div class="p-5 flex-grow space-y-4">
                @forelse($preventivosVencidos as $m)
                    <div class="flex items-center justify-between border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                                <x-heroicon-o-calendar-days class="w-4 h-4" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $m->equipo->nombre ?? 'N/A' }}</p>
                                <p class="text-[11px] text-gray-500 truncate w-40 md:w-64" title="{{ $m->descripcion }}">{{ $m->descripcion }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-rose-700 bg-rose-100 px-2 py-1 rounded shadow-sm whitespace-nowrap">
                            {{ optional($m->fecha)->translatedFormat('d M') ?? $m->fecha }}
                        </span>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 opacity-40">
                        <x-heroicon-o-check-circle class="w-10 h-10 text-emerald-400 mb-2"/>
                        <p class="text-xs text-gray-500 font-medium">Al día con preventivos</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Equipos Críticos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="bg-orange-50 px-5 py-4 border-b border-orange-100 flex items-center gap-2">
                <x-heroicon-s-fire class="w-5 h-5 text-orange-600" />
                <h3 class="font-bold text-orange-800 text-sm">Top Equipos Críticos (Top 5)</h3>
            </div>
            <div class="p-5 flex-grow space-y-4">
                @forelse($equiposCriticos as $equipo)
                    <div class="flex items-center justify-between border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600 font-bold text-xs">
                                {{ $loop->iteration }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $equipo->nombre }}</p>
                                <p class="text-[11px] text-gray-500">{{ $equipo->codigo }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-gray-900 block leading-none">{{ $equipo->mantenimientos_count }}</span>
                            <span class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Fallas</span>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 opacity-40">
                        <x-heroicon-o-shield-check class="w-10 h-10 text-emerald-400 mb-2"/>
                        <p class="text-xs text-gray-500 font-medium">Sin equipos críticos</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-indigo-500"/>
                Últimos Mantenimientos
            </h3>
            <a href="{{ route('mantenimientos.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 group">
                Ver todos <x-heroicon-o-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Equipo</th>
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Técnico</th>
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ultimosMantenimientos as $m)
                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform shadow-sm">
                                        <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $m->equipo->nombre ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $m->equipo->codigo ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    @if($m->usuario && $m->usuario->foto)
                                        <img src="{{ asset($m->usuario->foto) }}" class="w-8 h-8 rounded-full object-cover shadow-sm">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 shadow-sm">
                                            {{ substr($m->usuario->name ?? 'U', 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700">{{ $m->usuario->name ?? 'Sin asignar' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold shadow-sm {{ $m->tipo === 'preventivo' ? 'bg-sky-50 text-sky-700 border border-sky-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                                    <x-heroicon-s-sparkles class="w-3 h-3 mr-1" />
                                    {{ ucfirst($m->tipo) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm font-medium text-gray-600">
                                {{ optional($m->fecha)->translatedFormat('d M Y') ?? $m->fecha }}
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $statusColors = [
                                        'pendiente' => 'bg-amber-50 text-amber-700 border-amber-200 indicator-amber',
                                        'en_proceso' => 'bg-blue-50 text-blue-700 border-blue-200 indicator-blue',
                                        'finalizado' => 'bg-emerald-50 text-emerald-700 border-emerald-200 indicator-emerald',
                                    ];
                                    $colorClass = $statusColors[$m->estado] ?? 'bg-gray-50 text-gray-700 border-gray-200 indicator-gray';
                                    
                                    // Extract indicator color for the dot
                                    $dotColor = 'bg-gray-400';
                                    if(str_contains($colorClass, 'amber')) $dotColor = 'bg-amber-500';
                                    if(str_contains($colorClass, 'blue')) $dotColor = 'bg-blue-500';
                                    if(str_contains($colorClass, 'emerald')) $dotColor = 'bg-emerald-500';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-sm {{ $colorClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $dotColor }} shadow-[0_0_4px_currentColor]"></span>
                                    {{ ucfirst(str_replace('_', ' ', $m->estado)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-inbox class="w-12 h-12 text-gray-300 mb-3" />
                                    <p class="text-gray-500 font-medium">No hay mantenimientos recientes.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts for Chart.js and AJAX -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración general de Chart.js
            Chart.defaults.font.family = "'Inter', 'sans-serif'";
            Chart.defaults.color = '#9ca3af';
            Chart.defaults.scale.grid.color = '#f3f4f6';

            let chartMes, chartInversion, chartEquipos, chartTipo;

            // Constantes de colores para mantener consistencia
            const colors = {
                indigo: '#6366f1',
                emerald: '#10b981',
                rose: '#f43f5e',
                amber: '#f59e0b',
                sky: '#0ea5e9',
                gray: '#9ca3af'
            };

            const initCharts = () => {
                const ctxMes = document.getElementById('chartMantenimientosMes').getContext('2d');
                
                // Remover gradiente, usaremos un color sólido para las barras
                chartMes = new Chart(ctxMes, {
                    type: 'bar',
                    data: { labels: [], datasets: [] },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false }, 
                            tooltip: { 
                                mode: 'index', intersect: false,
                                backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                titleFont: { size: 13 },
                                bodyFont: { size: 13 },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false
                            } 
                        },
                        scales: { 
                            y: { beginAtZero: true, border: { display: false }, ticks: { padding: 10, precision: 0 } }, 
                            x: { grid: { display: false }, ticks: { padding: 10 } } 
                        },
                        animation: {
                            y: { duration: 1000, easing: 'easeOutQuart' }
                        }
                    }
                });

                const ctxInversion = document.getElementById('chartInversionMes').getContext('2d');
                chartInversion = new Chart(ctxInversion, {
                    type: 'bar',
                    data: { labels: [], datasets: [] },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: { 
                                backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return 'Inversión: S/ ' + context.parsed.y.toLocaleString('es-PE', { minimumFractionDigits: 2 });
                                    }
                                }
                            }
                        },
                        scales: { 
                            y: { beginAtZero: true, border: { display: false }, ticks: { callback: value => 'S/ ' + value } }, 
                            x: { grid: { display: false } } 
                        }
                    }
                });

                const ctxEquipos = document.getElementById('chartEstadoEquipos').getContext('2d');
                chartEquipos = new Chart(ctxEquipos, {
                    type: 'doughnut',
                    data: { labels: [], datasets: [] },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: { 
                            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 12 } } },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                padding: 15,
                                cornerRadius: 10,
                                bodyFont: { size: 13 },
                                callbacks: {
                                    afterLabel: function(context) {
                                        const status = context.label.toLowerCase().replace(/ /g, '_');
                                        const desglose = chartEquipos.data.desgloseData ? chartEquipos.data.desgloseData[status] : null;
                                        
                                        if (desglose) {
                                            let lines = ['', 'DETALLE POR TIPO:'];
                                            for (const [tipo, cant] of Object.entries(desglose)) {
                                                lines.push(`• ${tipo}: ${cant}`);
                                            }
                                            return lines;
                                        }
                                        return '';
                                    }
                                }
                            }
                        },
                        layout: { padding: { bottom: 20 } }
                    }
                });

                const ctxTipo = document.getElementById('chartMantenimientosTipo').getContext('2d');
                chartTipo = new Chart(ctxTipo, {
                    type: 'bar',
                    data: { labels: [], datasets: [] },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8 }
                        },
                        scales: { 
                            y: { beginAtZero: true, border: { display: false }, ticks: { precision: 0 } }, 
                            x: { grid: { display: false } } 
                        },
                        animation: {
                            y: { duration: 1000, easing: 'easeOutQuart' }
                        }
                    }
                });

                return null;
            };

            initCharts();

            // Animación de números para KPIs
            const animateValue = (id, start, end, duration, isCurrency = false) => {
                if (start === end) {
                    let obj = document.getElementById(id);
                    obj.innerHTML = isCurrency ? end.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : end;
                    return;
                }
                let obj = document.getElementById(id);
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const currentVal = progress * (end - start) + start;
                    obj.innerHTML = isCurrency 
                        ? currentVal.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) 
                        : Math.floor(currentVal);
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        obj.innerHTML = isCurrency ? end.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : end;
                    }
                };
                window.requestAnimationFrame(step);
            };

            const fetchDashboardData = async () => {
                try {
                    document.body.style.cursor = 'wait';
                    
                    const form = document.getElementById('filterForm');
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData).toString();
                    
                    const response = await fetch(`{{ route('dashboard.data') }}?${params}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Error al obtener datos');
                    
                    const result = await response.json();
                    
                    // 1. Actualizar KPIs
                    const kpiIds = {
                        'kpi-totalEquipos': result.kpis.totalEquipos,
                        'kpi-equiposOperativos': result.kpis.equiposOperativos,
                        'kpi-equiposConFalla': result.kpis.equiposConFalla,
                        'kpi-mantenimientosPendientes': result.kpis.mantenimientosPendientes,
                        'kpi-inversionAnual': result.kpis.inversionAnual,
                        'kpi-inversionMensual': result.kpis.inversionMensual
                    };

                    if (result.kpis.isFiltered) {
                        document.getElementById('label-inversionAnual').innerText = 'Inversión Total (Filtro)';
                        document.getElementById('label-inversionMensual').innerText = 'Promedio Mensual (Filtro)';
                    } else {
                        document.getElementById('label-inversionAnual').innerText = 'Inversión del Año';
                        document.getElementById('label-inversionMensual').innerText = 'Inversión del Mes';
                    }

                    for (const [id, value] of Object.entries(kpiIds)) {
                        const el = document.getElementById(id);
                        const isCurrency = id.includes('inversion');
                        const currentText = el.innerText.replace(/[^0-9.-]+/g, '');
                        const current = parseFloat(currentText) || 0;
                        animateValue(id, current, value, 800, isCurrency);
                    }

                    // 2. Mapeo de colores
                    const statusColorMap = {
                        'operativo': colors.emerald,
                        'con_falla': colors.rose,
                        'en_mantenimiento': colors.sky,
                        'dado_de_baja': colors.gray,
                        'default': colors.amber
                    };

                    // 3. Actualizar Gráfico Mantenimientos
                    chartMes.data.labels = result.charts.mantenimientosPorMes.labels;
                    chartMes.data.datasets = [{
                        label: 'Trabajos',
                        data: result.charts.mantenimientosPorMes.data,
                        backgroundColor: colors.indigo,
                        borderRadius: 6, barPercentage: 0.5
                    }];
                    chartMes.update();

                    // 4. Actualizar Gráfico Inversión
                    chartInversion.data.labels = result.charts.inversionPorMes.labels;
                    chartInversion.data.datasets = [{
                        label: 'Inversión',
                        data: result.charts.inversionPorMes.data,
                        backgroundColor: colors.emerald,
                        borderRadius: 6, barPercentage: 0.5
                    }];
                    chartInversion.update();

                    // 5. Actualizar Gráfico Estados (Doughnut)
                    const labelsEquipos = result.charts.estadoEquipos.labels || [];
                    const bgColorsEquipos = labelsEquipos.map(s => statusColorMap[String(s || '').toLowerCase()] || statusColorMap['default']);
                    
                    chartEquipos.data.labels = labelsEquipos.map(l => String(l || 'Desconocido').replace(/_/g, ' ').toUpperCase());
                    chartEquipos.data.desgloseData = result.charts.estadoEquipos.desglose;
                    chartEquipos.data.datasets = [{
                        data: result.charts.estadoEquipos.data,
                        backgroundColor: bgColorsEquipos,
                        borderWidth: 0, hoverOffset: 8
                    }];
                    chartEquipos.update();

                    // 6. Actualizar Gráfico Tipos
                    const labelsTipo = result.charts.mantenimientosPorTipo.labels || [];
                    const typeColors = labelsTipo.map(t => String(t || '').toLowerCase() === 'preventivo' ? colors.sky : colors.rose);
                    
                    chartTipo.data.labels = labelsTipo.map(l => String(l || 'Desconocido').toUpperCase());
                    chartTipo.data.datasets = [{
                        label: 'Cantidad',
                        data: result.charts.mantenimientosPorTipo.data,
                        backgroundColor: typeColors,
                        borderRadius: 6, barPercentage: 0.6
                    }];
                    chartTipo.update();

                } catch (error) {
                    console.error('Error AJAX Dashboard:', error);
                } finally {
                    document.body.style.cursor = 'default';
                }
            };

            // Event Listeners
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                fetchDashboardData();
            });

            document.getElementById('btnResetFilters').addEventListener('click', function() {
                document.getElementById('filterForm').reset();
                fetchDashboardData();
            });

            // Carga inicial
            fetchDashboardData();
        });
    </script>
    @endpush
</x-app-layout>
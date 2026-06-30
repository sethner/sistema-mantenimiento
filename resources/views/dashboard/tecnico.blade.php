<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Mi Panel de Trabajo</h2>
                <p class="text-sm text-gray-500">Resumen de tareas y mantenimientos asignados</p>
            </div>
            <div class="hidden sm:block">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sesión Activa
                </span>
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

    <div class="space-y-8">
        <!-- BIENVENIDA PREMIUM -->
        <div class="relative bg-gradient-to-r from-emerald-600 via-emerald-750 to-teal-800 rounded-3xl overflow-hidden shadow-xl shadow-emerald-100/50 p-6 sm:p-8 border border-emerald-500/10">
            <!-- Decoraciones de fondo -->
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-60 h-60 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/3 -mb-10 w-48 h-48 rounded-full bg-teal-400/20 blur-2xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-4 sm:gap-6">
                    <!-- Avatar o Icono llamativo -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 shadow-inner shrink-0 animate-bounce-slow">
                        <span class="text-3xl sm:text-4xl">🛠️</span>
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
                        <p class="text-sm sm:text-base text-emerald-100 mt-1 max-w-xl font-medium">
                            Bienvenido de nuevo a tu panel de trabajo. Tienes asignado el rol operativo para atender, diagnosticar e iniciar mantenimientos de los equipos.
                        </p>
                    </div>
                </div>
                
                <div class="bg-white/10 backdrop-blur-md px-5 py-4 rounded-2xl border border-white/20 flex flex-col items-center justify-center min-w-[150px] shrink-0 text-white self-stretch md:self-auto">
                    <span class="text-xs font-semibold text-emerald-250 uppercase tracking-wider">Fecha de hoy</span>
                    <span class="text-2xl font-black mt-1">{{ now()->format('d') }}</span>
                    <span class="text-xs font-bold text-emerald-100 mt-1 uppercase">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('MMMM, Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- KPIs Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- KPI Pendientes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                    <x-heroicon-s-clock class="w-24 h-24 text-amber-500" />
                </div>
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tareas Pendientes</p>
                        <p class="text-4xl font-bold text-gray-900 mt-2">{{ $tareasPendientes }}</p>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-amber-600 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-2 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                        <span>Requieren inicio</span>
                    </div>
                </div>
            </div>

            <!-- KPI En Proceso -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                    <x-heroicon-s-cog class="w-24 h-24 text-blue-500" />
                </div>
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Tareas en Proceso</p>
                        <p class="text-4xl font-bold text-gray-900 mt-2">{{ $tareasEnProceso }}</p>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-blue-600 font-medium">
                        <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
                        <span>Trabajando actualmente</span>
                    </div>
                </div>
            </div>

            <!-- KPI Finalizadas -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:shadow-md transition bg-gradient-to-br from-white to-emerald-50/30">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                    <x-heroicon-s-check-circle class="w-24 h-24 text-emerald-500" />
                </div>
                <div class="relative z-10 flex flex-col h-full justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Completadas Histórico</p>
                        <p class="text-4xl font-bold text-gray-900 mt-2">{{ $tareasCompletadas }}</p>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-emerald-600 font-medium">
                        <x-heroicon-o-chart-bar class="w-4 h-4 mr-1" />
                        <span>Rendimiento global</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas de Vencidos -->
        @if($mantenimientosVencidos->isNotEmpty())
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-2 opacity-10 pointer-events-none">
                    <x-heroicon-s-bell-alert class="w-32 h-32 text-rose-600" />
                </div>
                <div class="relative z-10">
                    <h3 class="font-bold text-rose-800 flex items-center gap-2 text-lg">
                        <x-heroicon-s-exclamation-circle class="w-6 h-6 animate-pulse" />
                        Atención: Tareas Vencidas
                    </h3>
                    <p class="text-sm text-rose-600 mt-1 mb-4">Las siguientes asignaciones han superado su fecha límite.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($mantenimientosVencidos as $m)
                            <a href="{{ route('mantenimientos.show', $m) }}" class="block bg-white border border-rose-100 p-4 rounded-xl shadow-sm hover:shadow-md hover:border-rose-300 transition group">
                                <div class="flex justify-between items-start mb-2">
                                    <p class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $m->equipo->nombre ?? 'N/A' }}</p>
                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wide">Vencido</span>
                                </div>
                                <p class="text-sm text-gray-500 line-clamp-2 mb-3 h-10">{{ $m->descripcion }}</p>
                                <div class="flex items-center text-xs text-rose-600 font-medium bg-rose-50/50 p-2 rounded-lg">
                                    <x-heroicon-o-calendar class="w-4 h-4 mr-1.5" />
                                    Asignado: {{ optional($m->fecha)->translatedFormat('d M, Y') ?? $m->fecha }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Dos Columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Próximos Mantenimientos (Pendientes/Proceso) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-indigo-500"/>
                        Mis Próximos Mantenimientos
                    </h3>
                </div>
                
                <div class="p-4 flex-grow">
                    <div class="space-y-4">
                        @forelse($proximosMantenimientos as $m)
                            <div class="group border border-gray-100 rounded-xl p-4 hover:border-indigo-100 hover:shadow-sm transition bg-white relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $m->estado === 'en_proceso' ? 'bg-blue-500' : 'bg-amber-400' }}"></div>
                                
                                <div class="flex justify-between items-start pl-3">
                                    <div>
                                        <a href="{{ route('mantenimientos.show', $m) }}" class="font-bold text-gray-900 hover:text-indigo-600 transition-colors inline-block">
                                            {{ $m->equipo->nombre ?? 'N/A' }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-gray-500">{{ $m->equipo->codigo ?? '' }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $m->tipo === 'preventivo' ? 'bg-sky-50 text-sky-700' : 'bg-rose-50 text-rose-700' }}">
                                                {{ $m->tipo }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right flex flex-col items-end">
                                        <span class="text-sm font-medium {{ $m->fecha->isToday() ? 'text-rose-600 font-bold' : 'text-gray-600' }}">
                                            {{ $m->fecha->isToday() ? 'Hoy' : (optional($m->fecha)->translatedFormat('d M') ?? $m->fecha) }}
                                        </span>
                                        <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $m->estado === 'en_proceso' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ str_replace('_', ' ', $m->estado) }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-3 pl-3 bg-gray-50 p-2 rounded-lg">{{ $m->descripcion }}</p>
                                
                                <div class="mt-3 pl-3 flex justify-end">
                                    <a href="{{ route('mantenimientos.show', $m) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group-hover:underline">
                                        Ver detalles <x-heroicon-o-arrow-right class="w-3 h-3" />
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 mb-4">
                                    <x-heroicon-o-face-smile class="w-8 h-8" />
                                </div>
                                <h4 class="text-base font-medium text-gray-900">Todo al día</h4>
                                <p class="text-sm text-gray-500 mt-1">No tienes mantenimientos próximos asignados.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Últimos Completados -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-heroicon-o-check-badge class="w-5 h-5 text-emerald-500"/>
                        Últimos Trabajos Completados
                    </h3>
                </div>

                <div class="p-4 flex-grow">
                    <div class="space-y-3">
                        @forelse($ultimosCompletados as $m)
                            <a href="{{ route('mantenimientos.show', $m) }}" class="block border border-gray-100 rounded-xl p-4 hover:bg-gray-50 transition group relative overflow-hidden">
                                <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                
                                <div class="flex justify-between items-center relative z-10">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                            <x-heroicon-s-check class="w-5 h-5" />
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900 group-hover:text-emerald-700 transition-colors">{{ $m->equipo->nombre ?? 'N/A' }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-xs text-gray-500">{{ $m->equipo->codigo ?? '' }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span class="text-xs font-medium text-gray-500">{{ optional($m->fecha)->translatedFormat('d M, Y') ?? $m->fecha }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400 group-hover:text-emerald-500 transition-colors transform group-hover:translate-x-1" />
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-10">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-400 mb-4">
                                    <x-heroicon-o-archive-box class="w-8 h-8" />
                                </div>
                                <h4 class="text-base font-medium text-gray-900">Aún no hay historial</h4>
                                <p class="text-sm text-gray-500 mt-1">Los trabajos que finalices aparecerán aquí.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    @if($ultimosCompletados->count() >= 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('mantenimientos.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            Ver historial completo &rarr;
                        </a>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

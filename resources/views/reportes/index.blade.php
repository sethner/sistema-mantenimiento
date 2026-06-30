@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <x-heroicon-o-document-chart-bar class="w-6 h-6 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Centro de Reportes</h2>
            <p class="text-sm text-gray-500">Genera documentos oficiales y análisis de datos del sistema.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto pb-12">

    @if(auth()->user()->hasRole('supervisor'))
        @php
            $user = auth()->user();
            $roleName = optional($user->roles->first())->nombre ?? 'Supervisor';
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

        <!-- BIENVENIDA PREMIUM PARA SUPERVISOR -->
        <div class="relative bg-gradient-to-r from-purple-600 via-indigo-650 to-indigo-800 rounded-3xl overflow-hidden shadow-xl shadow-indigo-100/50 p-6 sm:p-8 mb-8 border border-indigo-500/10 animate-in fade-in slide-in-from-top-4 duration-300">
            <!-- Decoraciones de fondo -->
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-60 h-60 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/3 -mb-10 w-48 h-48 rounded-full bg-purple-400/20 blur-2xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-4 sm:gap-6">
                    <!-- Avatar o Icono llamativo -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 shadow-inner shrink-0 animate-bounce-slow">
                        <span class="text-3xl sm:text-4xl">📊</span>
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
                            Bienvenido de nuevo al Centro de Reportes. Tienes acceso completo para generar, descargar e inspeccionar informes técnicos de los equipos.
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
    @endif
    
    @if(!auth()->user()->hasRole('tecnico'))
    {{-- RESUMEN EJECUTIVO (FEATURED CARD) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-2 bg-indigo-600 rounded-3xl p-8 shadow-xl shadow-indigo-200 relative overflow-hidden group">
            <div class="absolute top-0 right-0 -m-8 w-64 h-64 bg-white/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-white max-w-lg text-center md:text-left">
                    <h3 class="text-2xl font-bold mb-3">Resumen Ejecutivo de Gestión</h3>
                    <p class="text-indigo-100 text-sm leading-relaxed mb-6">
                        Genera un informe consolidado con las métricas clave de operatividad, mantenimiento y soporte técnico.
                    </p>
                    <div class="mt-6 flex items-center gap-2">
                    <a href="{{ route('reportes.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-indigo-600 rounded-xl text-sm font-bold hover:shadow-lg transition-all active:scale-95">
                        <x-heroicon-s-arrow-down-tray class="w-4 h-4" />
                        Descargar Reporte Ejecutivo
                    </a>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <x-heroicon-o-presentation-chart-line class="w-24 h-24 text-indigo-200/50" />
                </div>
            </div>
        </div>
        
        <div class="bg-emerald-500 rounded-3xl p-8 shadow-xl shadow-emerald-200 relative overflow-hidden group" x-data="{ showModal: false }">
            <div class="absolute top-0 right-0 -m-8 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-emerald-100 text-xs font-bold uppercase tracking-widest mb-1">Inversión Total</p>
                <h3 class="text-4xl font-black text-white mb-2">S/ {{ number_format($mantenimientos->sum('costo'), 2) }}</h3>
                <p class="text-emerald-100 text-xs leading-relaxed">
                    Monto total acumulado en {{ $mantenimientos->count() }} mantenimientos registrados hasta hoy.
                </p>
                <div class="mt-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                        <x-heroicon-s-currency-dollar class="w-5 h-5 text-white" />
                    </span>
                    <span class="text-white text-xs font-bold">Control de gastos activo</span>
                </div>
                <div class="mt-6 flex items-center gap-2">
                    <button @click="showModal = true" class="w-full py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                        Configurar Reporte
                        <x-heroicon-s-chevron-right class="w-3 h-3" />
                    </button>
                </div>
            </div>

            {{-- Modal Inversion --}}
            <template x-if="showModal">
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>
                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden animate-in zoom-in duration-200">
                        <form action="{{ route('reportes.inversion.pdf') }}" method="GET">
                            <div class="p-8">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Reporte de Inversión Total</h3>
                                <p class="text-sm text-gray-500 mb-6">Selecciona el periodo para filtrar los costos de mantenimiento.</p>
                                
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Año</label>
                                <select name="anio" class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:ring-2 focus:ring-indigo-500 outline-none transition mb-4">
                                    @php
                                        $currentYear = date('Y');
                                    @endphp
                                    @for($i = $currentYear; $i >= 2020; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mes (Opcional)</label>
                                <select name="mes" class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                    <option value="">Todo el año</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <div class="bg-gray-50 p-6 flex justify-end gap-3">
                                <button type="button" @click="showModal = false" class="px-6 py-2 text-sm font-bold text-gray-500">Cancelar</button>
                                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg">Descargar PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        @if(!auth()->user()->hasRole('tecnico'))
        {{-- INVENTARIO DE EQUIPOS --}}
        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col group" x-data="{ showModal: false }">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <x-heroicon-o-computer-desktop class="w-6 h-6" />
            </div>
            <h4 class="text-base font-bold text-gray-900 mb-2">Inventario de Equipos</h4>
            <p class="text-xs text-gray-500 leading-relaxed mb-8 flex-1">
                Reporte detallado de bienes tecnológicos, agrupados por tipo y con estado actual de cada componente.
            </p>
            <button @click="showModal = true" class="w-full py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                Configurar Reporte
                <x-heroicon-s-chevron-right class="w-3 h-3" />
            </button>

            {{-- Modal Inventario --}}
            <template x-if="showModal">
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>
                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden animate-in zoom-in duration-200">
                        <form action="{{ route('reportes.bienes.pdf') }}" method="GET">
                            <div class="p-8">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Inventario de Equipos</h3>
                                <p class="text-sm text-gray-500 mb-6">Selecciona el alcance del reporte de inventario.</p>
                                
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Alcance del Reporte</label>
                                <select name="equipo_id" class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                    <option value="">Todos los equipos (General)</option>
                                    @foreach($equipos as $equipo)
                                        <option value="{{ $equipo->id }}">{{ $equipo->codigo }} - {{ $equipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="bg-gray-50 p-6 flex justify-end gap-3">
                                <button type="button" @click="showModal = false" class="px-6 py-2 text-sm font-bold text-gray-500">Cancelar</button>
                                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg">Descargar PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
        @endif

        @if(!auth()->user()->hasRole('tecnico'))
        {{-- HISTORIAL DE MANTENIMIENTO --}}
        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col group" x-data="{ showModal: false }">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <x-heroicon-o-wrench-screwdriver class="w-6 h-6" />
            </div>
            <h4 class="text-base font-bold text-gray-900 mb-2">Historial de Mantenimiento</h4>
            <p class="text-xs text-gray-500 leading-relaxed mb-8 flex-1">
                Ficha de intervenciones técnicas, incluyendo mantenimientos preventivos y correctivos por equipo.
            </p>
            <button @click="showModal = true" class="w-full py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                Configurar Reporte
                <x-heroicon-s-chevron-right class="w-3 h-3" />
            </button>

            {{-- Modal Mantenimiento --}}
            <template x-if="showModal">
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>
                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden animate-in zoom-in duration-200">
                        <form action="{{ route('reportes.mantenimientos.pdf') }}" method="GET">
                            <div class="p-8">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Historial Clínico</h3>
                                <p class="text-sm text-gray-500 mb-6">Selecciona el equipo para ver su historial de servicio.</p>
                                
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Equipo</label>
                                <select name="equipo_id" class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                    <option value="">General (Todos los equipos)</option>
                                    @foreach($equipos as $equipo)
                                        <option value="{{ $equipo->id }}">{{ $equipo->codigo }} - {{ $equipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="bg-gray-50 p-6 flex justify-end gap-3">
                                <button type="button" @click="showModal = false" class="px-6 py-2 text-sm font-bold text-gray-500">Cancelar</button>
                                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg">Descargar PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
        @endif

        {{-- DESEMPEÑO TÉCNICO --}}
        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col group" x-data="{ showModal: false }">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <x-heroicon-o-user-group class="w-6 h-6" />
            </div>
            <h4 class="text-base font-bold text-gray-900 mb-2">Desempeño Técnico</h4>
            <p class="text-xs text-gray-500 leading-relaxed mb-8 flex-1">
                Reporte de carga de trabajo y efectividad por cada técnico asignado al sistema.
            </p>
            <button @click="showModal = true" class="w-full py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                Configurar Reporte
                <x-heroicon-s-chevron-right class="w-3 h-3" />
            </button>

            {{-- Modal Técnico --}}
            <template x-if="showModal">
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>
                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden animate-in zoom-in duration-200">
                        <form action="{{ route('reportes.tecnico.pdf') }}" method="GET">
                            <div class="p-8">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Carga de Trabajo</h3>
                                <p class="text-sm text-gray-500 mb-6">Filtra las actividades por técnico responsable.</p>
                                
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Personal Técnico</label>
                                <select name="tecnico_id" class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                    <option value="">Resumen General (Técnicos)</option>
                                    @foreach($tecnicos as $tecnico)
                                        <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="bg-gray-50 p-6 flex justify-end gap-3">
                                <button type="button" @click="showModal = false" class="px-6 py-2 text-sm font-bold text-gray-500">Cancelar</button>
                                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg">Descargar PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>

        @if(!auth()->user()->hasRole('tecnico'))
        {{-- EQUIPOS DADOS DE BAJA --}}
        <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col group">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <x-heroicon-o-trash class="w-6 h-6" />
            </div>
            <h4 class="text-base font-bold text-gray-900 mb-2">Equipos de Baja</h4>
            <p class="text-xs text-gray-500 leading-relaxed mb-8 flex-1">
                Listado de dispositivos retirados del servicio por obsolescencia o daño irreparable.
            </p>
            <a href="{{ route('reportes.baja.pdf') }}" class="w-full py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                Generar Reporte
                <x-heroicon-s-chevron-right class="w-3 h-3" />
            </a>
        </div>
        @endif

    </div>
</div>
@endsection

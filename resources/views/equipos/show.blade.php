@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
    <div class="flex items-center gap-3">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $equipo->nombre }}</h1>
        @php
            $estadoClase = match($equipo->estado) {
                'operativo'        => 'bg-emerald-100 text-emerald-800 ring-emerald-300/50 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-800/50',
                'en_mantenimiento' => 'bg-amber-100 text-amber-800 ring-amber-300/50 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-800/50',
                'con_falla'        => 'bg-red-100 text-red-800 ring-red-300/50 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-800/50',
                'dado_de_baja'     => 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700',
                default            => 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700',
            };
        @endphp
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 {{ $estadoClase }}">
            {{ ucwords(str_replace('_', ' ', $equipo->estado)) }}
        </span>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <button onclick="analisisPredictivo({{ $equipo->id }})"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:border-indigo-300 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800 dark:hover:bg-indigo-900/50 transition-all duration-200 shadow-sm">
            <x-heroicon-s-sparkles class="w-4 h-4" />
            Análisis Predictivo
        </button>
        <a href="{{ route('equipos.edit', $equipo) }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:text-slate-900 dark:bg-slate-800 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white transition-all duration-200 shadow-sm">
            <x-heroicon-o-pencil-square class="w-4 h-4" />
            Editar
        </a>
        <a href="{{ route('equipos.index') }}"
            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
    </div>
</div>
@endsection

@section('content')
@php
    $totalComp      = $equipo->componentes->count();
    $buenos         = $equipo->componentes->filter(fn($c) => $c->pivot->estado === 'bueno')->count();
    $regulares      = $equipo->componentes->filter(fn($c) => $c->pivot->estado === 'regular')->count();
    $malos          = $equipo->componentes->filter(fn($c) => $c->pivot->estado === 'malo')->count();
    $reemplazados   = $equipo->componentes->filter(fn($c) => $c->pivot->estado === 'reemplazado')->count();

    $pctBueno       = $totalComp > 0 ? round(($buenos / $totalComp) * 100) : 100;
    $pctRegular     = $totalComp > 0 ? round(($regulares / $totalComp) * 100) : 0;
    $pctMalo        = $totalComp > 0 ? round(($malos / $totalComp) * 100) : 0;

    $costoTotal     = $equipo->mantenimientos->sum('costo');
    $numMant        = $equipo->mantenimientos->count();
    $totalFallas    = $equipo->historialFallas->count();

    $diasRestantes  = null;
    $vencido        = false;
    if ($equipo->proximo_mantenimiento) {
        $diff = now()->startOfDay()->diffInDays($equipo->proximo_mantenimiento->startOfDay(), false);
        $vencido = $diff < 0;
        $diasRestantes = abs($diff);
    }

    // Actividad unificada
    $actividades = collect();
    foreach ($equipo->mantenimientos as $m) {
        $actividades->push(['tipo' => 'mant', 'fecha' => $m->fecha, 'titulo' => ucfirst($m->tipo), 'desc' => $m->descripcion, 'estado' => $m->estado, 'link' => route('mantenimientos.show', $m)]);
    }
    foreach ($equipo->historialFallas as $f) {
        $actividades->push(['tipo' => 'falla', 'fecha' => $f->fecha, 'titulo' => 'Falla registrada', 'desc' => $f->descripcion, 'estado' => $f->resolucion ? 'resuelta' : 'activa', 'link' => null]);
    }
    $actividades = $actividades->sortByDesc('fecha')->take(6)->values();
@endphp

<div class="max-w-7xl mx-auto space-y-8 pb-12">

    @include('components.session.success')
    @include('components.session.error')

    {{-- FILA DE ESTADÍSTICAS --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-200 dark:divide-slate-800">
            
            {{-- Próximo Mantenimiento --}}
            <div class="p-6 flex items-start gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <div class="w-12 h-12 shrink-0 rounded-full bg-sky-50 dark:bg-sky-900/20 flex items-center justify-center text-sky-600 dark:text-sky-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 19.5m-18 0v-3h18v3" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Próximo mantenimiento</p>
                    @if($equipo->proximo_mantenimiento)
                        <p class="text-2xl font-bold text-slate-900 dark:text-white truncate">
                            {{ $equipo->proximo_mantenimiento->format('d M') }}
                            <span class="text-base font-normal text-slate-400">{{ $equipo->proximo_mantenimiento->format('Y') }}</span>
                        </p>
                        <p class="mt-1 text-sm font-semibold {{ $vencido ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            @if($vencido)
                                Vencido hace {{ $diasRestantes }} {{ $diasRestantes === 1 ? 'día' : 'días' }}
                            @else
                                Faltan {{ $diasRestantes }} {{ $diasRestantes === 1 ? 'día' : 'días' }}
                            @endif
                        </p>
                    @else
                        <p class="text-2xl font-bold text-slate-400 dark:text-slate-500">—</p>
                        <p class="mt-1 text-sm text-slate-500 italic">Sin fecha programada</p>
                    @endif
                    <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Ciclo: cada <span class="font-medium text-slate-700 dark:text-slate-300">{{ $equipo->frecuencia_mantenimiento ?? 6 }} meses</span>
                    </div>
                </div>
            </div>

            {{-- Fallas --}}
            <div class="p-6 flex items-start gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <div class="w-12 h-12 shrink-0 rounded-full bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600 dark:text-rose-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Fallas registradas</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $totalFallas }}</p>
                    <div class="mt-2 text-sm text-slate-500 dark:text-slate-400 space-y-1">
                        <p>Código: <span class="font-mono bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs font-medium text-slate-700 dark:text-slate-300">{{ $equipo->codigo }}</span></p>
                        <p>Tipo: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $equipo->tipo->nombre }}</span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- COLUMNA PRINCIPAL (Izquierda) --}}
        <div class="lg:col-span-8 space-y-8">
            
            {{-- Información General --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/20">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>
                        Información del equipo
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Código</dt>
                            <dd class="text-sm font-medium text-slate-900 dark:text-white font-mono">{{ $equipo->codigo }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Nombre</dt>
                            <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $equipo->nombre }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tipo</dt>
                            <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $equipo->tipo->nombre }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Marca</dt>
                            <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $equipo->marca ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Modelo</dt>
                            <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $equipo->modelo ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Registrado</dt>
                            <dd class="text-sm font-medium text-slate-900 dark:text-white">{{ $equipo->created_at->format('d/m/Y') }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Componentes --}}
            <div x-data="{ agregar: false }" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/20">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                        </svg>
                        Componentes
                        <span class="text-xs font-bold text-slate-600 bg-slate-200 dark:bg-slate-700 dark:text-slate-300 px-2.5 py-1 rounded-full ml-1">{{ $totalComp }}</span>
                    </h2>
                    <button @click="agregar = !agregar"
                            class="text-sm text-indigo-600 dark:text-indigo-400 font-semibold hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar
                    </button>
                </div>

                <div x-show="agregar" x-collapse x-cloak class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/30">
                    <form action="{{ route('equipos.componentes.agregar', $equipo) }}" method="POST" class="flex gap-3">
                        @csrf
                        <input type="text" name="nombre" placeholder="Ej. Memoria RAM 8GB DDR4" required
                               class="flex-1 text-sm border border-slate-300 dark:border-slate-600 rounded-xl px-4 py-2 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition shadow-sm">
                        <button type="submit"
                                class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shrink-0 shadow-sm">
                            Vincular
                        </button>
                    </form>
                </div>

                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($equipo->componentes as $c)
                        @php
                            $badgeClase = match($c->pivot->estado) {
                                'bueno'       => 'bg-emerald-100 text-emerald-800 ring-emerald-300/50 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-800/50',
                                'regular'     => 'bg-amber-100 text-amber-800 ring-amber-300/50 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-800/50',
                                'malo'        => 'bg-red-100 text-red-800 ring-red-300/50 dark:bg-red-900/30 dark:text-red-400 dark:ring-red-800/50',
                                'reemplazado' => 'bg-blue-100 text-blue-800 ring-blue-300/50 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-800/50',
                                default       => 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700',
                            };
                            $bgColor = match($c->pivot->estado) {
                                'bueno'       => 'bg-emerald-50/50 border-emerald-200 dark:bg-emerald-900/10 dark:border-emerald-800/50',
                                'regular'     => 'bg-amber-50/50 border-amber-200 dark:bg-amber-900/10 dark:border-amber-800/50',
                                'malo'        => 'bg-red-50/50 border-red-200 dark:bg-red-900/10 dark:border-red-800/50',
                                'reemplazado' => 'bg-blue-50/50 border-blue-200 dark:bg-blue-900/10 dark:border-blue-800/50',
                                default       => 'bg-slate-50 border-slate-200 dark:bg-slate-800/50 dark:border-slate-700',
                            };
                        @endphp
                        <div class="border rounded-xl p-4 flex flex-col justify-between gap-4 transition-colors duration-200 {{ $bgColor }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate" title="{{ $c->nombre }}">{{ $c->nombre }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $c->categoria->nombre ?? 'General' }}</p>
                                </div>
                                <form action="{{ route('equipos.componentes.quitar', [$equipo, $c]) }}" method="POST"
                                      onsubmit="return confirm('¿Desvincular este componente?')" class="shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-1 rounded-lg hover:bg-white/50 dark:hover:bg-slate-800">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                            <div>
                                <span class="inline-flex text-xs font-semibold px-2.5 py-1 rounded-full ring-1 {{ $badgeClase }}">
                                    {{ ucfirst($c->pivot->estado) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-10 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                            <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <span class="text-sm font-medium">Ningún componente vinculado aún.</span>
                        </div>
                    @endforelse
                </div>
            </div>
            
        </div>
        
        {{-- COLUMNA LATERAL (Derecha) --}}
        <div class="lg:col-span-4 space-y-8">

            {{-- Historial de mantenimientos --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/20">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mantenimientos
                        <span class="text-xs font-bold text-slate-600 bg-slate-200 dark:bg-slate-700 dark:text-slate-300 px-2.5 py-1 rounded-full ml-1">{{ $numMant }}</span>
                    </h2>
                    @if($numMant > 5)
                        <a href="{{ route('mantenimientos.index', ['search' => $equipo->codigo]) }}"
                           class="text-sm text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                            Ver todos
                        </a>
                    @endif
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($equipo->mantenimientos->sortByDesc('fecha')->take(5) as $m)
                    @php
                        $tipoClase  = $m->tipo === 'preventivo'
                            ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-300 dark:ring-indigo-800/50'
                            : 'bg-rose-50 text-rose-700 ring-1 ring-rose-200 dark:bg-rose-900/20 dark:text-rose-300 dark:ring-rose-800/50';
                        $estadoClase = match($m->estado) {
                            'pendiente'  => 'text-amber-600 dark:text-amber-400',
                            'en_proceso' => 'text-blue-600 dark:text-blue-400',
                            'finalizado' => 'text-emerald-600 dark:text-emerald-400',
                            default      => 'text-slate-500',
                        };
                    @endphp
                    <div class="flex items-start gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="shrink-0 mt-0.5">
                            <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded {{ $tipoClase }}">
                                {{ $m->tipo }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">{{ $m->descripcion }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $m->usuario->name ?? '—' }} · {{ $m->fecha->format('d/m/Y') }}
                                @if($m->costo) · <span class="font-semibold text-emerald-600 dark:text-emerald-400">S/. {{ number_format($m->costo, 0) }}</span> @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs font-semibold {{ $estadoClase }}">
                                {{ ucwords(str_replace('_', ' ', $m->estado)) }}
                            </span>
                            <a href="{{ route('mantenimientos.show', $m) }}"
                               class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800">
                                <x-heroicon-o-arrow-right class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-slate-400 dark:text-slate-500 italic">
                        Sin mantenimientos registrados.
                    </div>
                @endforelse
                </div>
            </div>

            {{-- Historial de fallas --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/20">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        Fallas
                        <span class="text-xs font-bold text-slate-600 bg-slate-200 dark:bg-slate-700 dark:text-slate-300 px-2.5 py-1 rounded-full ml-1">{{ $totalFallas }}</span>
                    </h2>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($equipo->historialFallas->sortByDesc('fecha')->take(5) as $f)
                    @php
                        $badgeFalla = $f->resolucion
                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:ring-emerald-800/50'
                            : 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-900/20 dark:text-rose-400 dark:ring-rose-800/50';
                        
                        $estadoFallaClase = $f->resolucion
                            ? 'text-emerald-600 dark:text-emerald-400'
                            : 'text-rose-600 dark:text-rose-400';
                        
                        $estadoFallaTexto = $f->resolucion ? 'Resuelta' : 'Activa';
                    @endphp
                    <div class="flex items-start gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <div class="shrink-0 mt-0.5">
                            <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded ring-1 {{ $badgeFalla }}">
                                {{ $f->componente ? 'Comp.' : 'General' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-200 truncate" title="{{ $f->descripcion }}">{{ $f->descripcion }}</p>
                            <div class="text-xs text-slate-500 mt-1 space-y-0.5">
                                <p>{{ $f->fecha ? $f->fecha->format('d/m/Y') : '—' }}</p>
                                @if($f->componente) <p>Componente: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $f->componente->nombre }}</span></p> @endif
                                @if($f->resolucion) <p>Resol.: <span class="italic text-slate-700 dark:text-slate-300">{{ $f->resolucion }}</span></p> @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs font-semibold {{ $estadoFallaClase }}">
                                {{ $estadoFallaTexto }}
                            </span>
                            @if($f->mantenimiento_id)
                                <a href="{{ route('mantenimientos.show', $f->mantenimiento_id) }}"
                                   class="text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800"
                                   title="Ver mantenimiento asociado">
                                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-slate-400 dark:text-slate-500 italic">
                        Sin fallas registradas.
                    </div>
                @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal IA --}}
<div id="modalIA" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="cerrarModal()"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 ring-1 ring-indigo-200 dark:ring-indigo-800/50">
                        <x-heroicon-s-sparkles class="w-5 h-5" />
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Análisis Predictivo IA</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Generado a partir del historial del equipo</p>
                    </div>
                </div>
                <button onclick="cerrarModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            <div id="loadingIA" class="px-6 py-16 flex flex-col items-center gap-4 text-center">
                <div class="w-10 h-10 border-4 border-indigo-100 dark:border-slate-800 border-t-indigo-600 rounded-full animate-spin"></div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Analizando métricas e historial del equipo...</p>
            </div>

            <div id="contentIA" class="hidden px-6 py-6 max-h-[60vh] overflow-y-auto">
                <div class="flex gap-3 p-4 bg-indigo-50/50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800/50 rounded-xl mb-6">
                    <x-heroicon-s-information-circle class="w-5 h-5 text-indigo-500 dark:text-indigo-400 shrink-0 mt-0.5" />
                    <p class="text-sm text-indigo-700 dark:text-indigo-300">Análisis predictivo basado en el registro de mantenimientos, antigüedad y componentes de este equipo.</p>
                </div>
                <div id="textoIA" class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed space-y-4"></div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 flex justify-end">
                <button onclick="cerrarModal()"
                        class="px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200 shadow-sm hover:shadow">
                    Cerrar panel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Lógica para realizar la consulta AJAX de Análisis Predictivo con Inteligencia Artificial
async function analisisPredictivo(id) {
    const modal   = document.getElementById('modalIA');
    const loading = document.getElementById('loadingIA');
    const content = document.getElementById('contentIA');
    const texto   = document.getElementById('textoIA');

    // Mostrar el modal y el indicador de carga, ocultar el contenedor de texto previo
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    content.classList.add('hidden');
    texto.innerHTML = '';

    try {
        // Enviar la petición POST con el token CSRF para realizar la predicción
        const res  = await fetch(`{{ url('ai/analisis-predictivo') }}/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await res.json();

        if (data.analisis) {
            // Reemplazar la sintaxis markdown de negritas **texto** por etiquetas html strong
            texto.innerHTML = data.analisis.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            loading.classList.add('hidden');
            content.classList.remove('hidden');
        } else {
            alert(data.error || 'No se pudo generar el análisis');
            cerrarModal();
        }
    } catch (e) {
        alert('Error de conexión');
        cerrarModal();
    }
}

// Oculta el modal de análisis predictivo de IA
function cerrarModal() {
    document.getElementById('modalIA').classList.add('hidden');
}
</script>
@endpush
@endsection

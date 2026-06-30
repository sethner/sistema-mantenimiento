@extends('layouts.app')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
    <div class="flex items-center gap-3">
        <a href="{{ route('mantenimientos.index') }}" class="group flex items-center justify-center w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200">
            <svg class="w-5 h-5 text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Atención Técnica</h1>
                @php
                $estadoBadge = match($mantenimiento->estado) {
                    'pendiente' => 'bg-amber-100 text-amber-800 ring-amber-300/50 dark:bg-amber-900/30 dark:text-amber-400 dark:ring-amber-800/50',
                    'en_proceso' => 'bg-blue-100 text-blue-800 ring-blue-300/50 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-800/50',
                    'finalizado' => 'bg-emerald-100 text-emerald-800 ring-emerald-300/50 dark:bg-emerald-900/30 dark:text-emerald-400 dark:ring-emerald-800/50',
                    default => 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-700',
                };
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 {{ $estadoBadge }}">
                    {{ ucwords(str_replace('_', ' ', $mantenimiento->estado)) }}
                </span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Consulte y actualice los hallazgos técnicos del servicio.</p>
        </div>
    </div>
</div>
@endsection

@section('content')
@php
$tipoBadge = $mantenimiento->tipo === 'preventivo'
    ? 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-300 dark:ring-indigo-800/50'
    : 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-900/20 dark:text-rose-300 dark:ring-rose-800/50';
@endphp

<div class="max-w-7xl mx-auto space-y-8 pb-12">

    @include('components.session.success')

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl shadow-sm dark:bg-red-900/20 dark:border-red-800/50 dark:text-red-300 flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <h3 class="font-semibold">Por favor, corrige los siguientes errores:</h3>
                <ul class="list-disc pl-5 mt-2 space-y-1 text-sm opacity-90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- FILA DE ESTADÍSTICAS --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200 dark:divide-slate-800">
            {{-- Equipo --}}
            <div class="p-6 flex items-start gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <div class="w-12 h-12 shrink-0 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Equipo</p>
                    <p class="text-base font-bold text-slate-900 dark:text-white truncate">{{ $mantenimiento->equipo->nombre }}</p>
                    <div class="mt-1 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                        <span class="font-mono bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-xs">{{ $mantenimiento->equipo->codigo }}</span>
                        <span class="truncate">{{ $mantenimiento->equipo->marca ?? 'S/M' }} · {{ $mantenimiento->equipo->modelo ?? 'S/MOD' }}</span>
                    </div>
                </div>
            </div>

            {{-- Tipo & Fecha --}}
            <div class="p-6 flex items-start gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <div class="w-12 h-12 shrink-0 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 19.5m-18 0v-3h18v3" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Modalidad</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold ring-1 {{ $tipoBadge }}">
                        {{ ucfirst($mantenimiento->tipo ?? 'correctivo') }}
                    </span>
                    <div class="mt-2 text-sm text-slate-500 dark:text-slate-400 space-y-1">
                        @if($mantenimiento->tipo === 'preventivo')
                        <p>Programada: <span class="font-medium text-slate-700 dark:text-slate-300">{{ optional($mantenimiento->fecha)->format('d/m/Y') ?? 'Sin fecha' }}</span></p>
                        @if($mantenimiento->proxima_fecha)
                        <p>Ejecución: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $mantenimiento->proxima_fecha->format('d/m/Y') }}</span></p>
                        @endif
                        @else
                        <p>Ejecución: <span class="font-medium text-slate-700 dark:text-slate-300">{{ optional($mantenimiento->fecha)->format('d/m/Y') ?? 'Sin fecha' }}</span></p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Técnico & Costo --}}
            <div class="p-6 flex items-start gap-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <div class="w-12 h-12 shrink-0 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Técnico asignado</p>
                    <p class="text-base font-bold text-slate-900 dark:text-white">{{ $mantenimiento->usuario->name ?? 'No asignado' }}</p>
                    <div class="mt-2 text-sm">
                        @if($mantenimiento->costo)
                        <p class="text-slate-600 dark:text-slate-400">Costo: <span class="font-semibold text-emerald-600 dark:text-emerald-400">S/. {{ number_format($mantenimiento->costo, 2) }}</span></p>
                        @else
                        <p class="italic text-slate-400 dark:text-slate-500">Sin costo registrado</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORMULARIO --}}
    <form action="{{ route('mantenimientos.update', $mantenimiento) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="tipo" value="{{ $mantenimiento->tipo ?? 'correctivo' }}">
        <input type="hidden" name="proxima_fecha" value="{{ optional($mantenimiento->proxima_fecha)->format('Y-m-d') }}">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-8">
            
            {{-- COLUMNA PRINCIPAL (Izquierda) --}}
            <div class="lg:col-span-8 space-y-8">
                
                {{-- Detalles del Servicio --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/20">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Detalles del Servicio
                        </h2>
                        <button type="button" id="btnAI"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:border-indigo-300 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800 dark:hover:bg-indigo-900/50 transition-all duration-200 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l8.904-4.43c.518-.258 1.096-.282 1.633-.067L21 17.25V9.75M9.813 15.904L9 21M9.813 15.904c-.381-.19-.808-.246-1.222-.162L3.5 17.25V9.75m6.313 6.154L21 9.75M9.813 15.904L3.5 9.75m17.5 0L12 3 3.5 9.75" />
                            </svg>
                            Consultar IA
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Panel IA --}}
                        <div id="panelAI" class="hidden">
                            <div class="bg-indigo-50/50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800/50 rounded-xl overflow-hidden">
                                <div class="px-5 py-3 border-b border-indigo-100 dark:border-indigo-800/50 flex justify-between items-center bg-white/50 dark:bg-slate-900/50">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        <span class="text-sm font-bold text-indigo-900 dark:text-indigo-300">Sugerencia de Diagnóstico IA</span>
                                    </div>
                                    <button type="button" id="btnCopiarAI"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-all duration-200 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 006.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 00-9-9z" />
                                        </svg>
                                        Copiar Datos
                                    </button>
                                </div>
                                <div id="iaResultado" class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6"></div>
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-xl p-5">
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Descripción original reportada</p>
                            <p class="text-slate-700 dark:text-slate-300 leading-relaxed italic">"{{ $mantenimiento->descripcion }}"</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2">
                                <label for="diagnostico" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Hallazgos y diagnóstico <span class="text-red-500">*</span>
                                </label>
                                <textarea id="diagnostico" name="diagnostico" rows="4"
                                    class="w-full text-sm border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-3 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-shadow placeholder-slate-400 resize-y shadow-sm"
                                    placeholder="Describa el estado real encontrado...">{{ old('diagnostico', $mantenimiento->diagnostico) }}</textarea>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                                    Acción técnica realizada <span class="text-red-500">*</span>
                                </label>
                                <textarea name="accion" rows="4"
                                    class="w-full text-sm border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-3 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-shadow placeholder-slate-400 resize-y shadow-sm"
                                    placeholder="Especifique los cambios o reparaciones...">{{ old('accion', $mantenimiento->accion) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Componentes --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/20">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                            </svg>
                            Validación de componentes
                        </h2>
                        <span class="text-xs font-bold text-slate-600 bg-slate-200 dark:bg-slate-700 dark:text-slate-300 px-2.5 py-1 rounded-full">{{ $mantenimiento->equipo->componentes->count() }}</span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($mantenimiento->equipo->componentes as $c)
                            @php
                            $dotColor = match($c->pivot->estado) {
                            'bueno' => 'bg-emerald-500',
                            'regular' => 'bg-amber-500',
                            'malo' => 'bg-red-500',
                            'reemplazado' => 'bg-blue-500',
                            default => 'bg-slate-400',
                            };
                            $bgColor = match($c->pivot->estado) {
                            'bueno' => 'bg-emerald-50/50 border-emerald-200 dark:bg-emerald-900/10 dark:border-emerald-800/50',
                            'regular' => 'bg-amber-50/50 border-amber-200 dark:bg-amber-900/10 dark:border-amber-800/50',
                            'malo' => 'bg-red-50/50 border-red-200 dark:bg-red-900/10 dark:border-red-800/50',
                            'reemplazado' => 'bg-blue-50/50 border-blue-200 dark:bg-blue-900/10 dark:border-blue-800/50',
                            default => 'bg-slate-50 border-slate-200 dark:bg-slate-800/50 dark:border-slate-700',
                            };
                            @endphp
                            <div class="flex items-center justify-between p-4 border rounded-xl {{ $bgColor }} transition-colors duration-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-2.5 h-2.5 rounded-full shadow-sm {{ $dotColor }}"></div>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $c->nombre }}</span>
                                </div>
                                <div class="relative w-36">
                                    <select name="componentes[{{ $c->id }}]"
                                        class="w-full text-sm font-medium border-slate-300 dark:border-slate-600 rounded-lg py-2 pl-3 pr-8 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-shadow shadow-sm cursor-pointer appearance-none">
                                        <option value="bueno" {{ $c->pivot->estado == 'bueno' ? 'selected' : '' }}>Bueno</option>
                                        <option value="regular" {{ $c->pivot->estado == 'regular' ? 'selected' : '' }}>Regular</option>
                                        <option value="malo" {{ $c->pivot->estado == 'malo' ? 'selected' : '' }}>Malo</option>
                                        <option value="reemplazado" {{ $c->pivot->estado == 'reemplazado' ? 'selected' : '' }}>Reemplazado</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-span-full py-10 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                <span class="text-sm font-medium">Sin componentes asociados.</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA LATERAL (Derecha) --}}
            <div class="lg:col-span-4 space-y-8">
                
                {{-- Gestión --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014-8.81c-1.5-.548-3.13-.85-4.82-.85-1.69 0-3.32.302-4.82.85m10.654 8.81c.548 1.5.85 3.13.85 4.82 0 1.69-.302 3.32-.85-4.82m-10.654-8.81a23.91 23.91 0 00-1.014 5.395m1.014-8.81c.548-1.5.85-3.13.85-4.82 0-1.69-.302-3.32-.85-4.82" />
                            </svg>
                            Gestión y Estado
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Costo --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Costo total del servicio (S/)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-slate-500 font-medium">S/</span>
                                </div>
                                <input type="number" step="0.01" name="costo" value="{{ old('costo', $mantenimiento->costo) }}"
                                    class="w-full pl-11 pr-4 py-3 text-sm font-medium border border-slate-300 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition shadow-sm"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <hr class="border-slate-200 dark:border-slate-800">

                        {{-- Estado --}}
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Actualizar estado</label>
                            <input type="hidden" name="estado" id="input_estado" value="{{ old('estado', $mantenimiento->estado) }}">

                            <div class="flex flex-col gap-3">
                                <button type="button" onclick="selectStatus('pendiente')" id="btn_pendiente"
                                    class="status-btn w-full flex items-center justify-between px-5 py-3 rounded-xl border transition-all duration-200 font-medium text-slate-600 border-slate-200 bg-white hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 shadow-sm active:scale-[0.98]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2.5 h-2.5 rounded-full bg-amber-400 indicator shadow-sm"></div>
                                        <span>Pendiente</span>
                                    </div>
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </button>

                                <button type="button" onclick="selectStatus('en_proceso')" id="btn_en_proceso"
                                    class="status-btn w-full flex items-center justify-between px-5 py-3 rounded-xl border transition-all duration-200 font-medium text-slate-600 border-slate-200 bg-white hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 shadow-sm active:scale-[0.98]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2.5 h-2.5 rounded-full bg-blue-500 indicator shadow-sm"></div>
                                        <span>Proceso</span>
                                    </div>
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                                </button>

                                <button type="button" onclick="selectStatus('finalizado')" id="btn_finalizado"
                                    class="status-btn w-full flex items-center justify-between px-5 py-3 rounded-xl border transition-all duration-200 font-medium text-slate-600 border-slate-200 bg-white hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 shadow-sm active:scale-[0.98]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 indicator shadow-sm"></div>
                                        <span>Finalizado</span>
                                    </div>
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Evidencias --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/20">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                            Evidencias
                        </h2>
                        <span id="badge_fotos_count" class="text-xs font-bold text-slate-600 bg-slate-200 dark:bg-slate-700 dark:text-slate-300 px-2.5 py-1 rounded-full">{{ $mantenimiento->fotos->count() }}</span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4" id="preview_fotos_container">
                            @foreach($mantenimiento->fotos as $foto)
                            <div class="aspect-square rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden relative group shadow-sm">
                                <img src="{{ asset('storage/' . $foto->ruta) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <!-- Botón para eliminar foto existente -->
                                <button type="button" onclick="eliminarFotoExistente({{ $foto->id }})" 
                                    class="absolute top-2 right-2 p-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                            <label for="ui_fotos" class="aspect-square rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:border-indigo-500 dark:hover:bg-indigo-900/20 flex flex-col items-center justify-center transition-all cursor-pointer group">
                                <svg class="w-6 h-6 text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="text-xs text-slate-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 font-semibold">Añadir</span>
                            </label>
                            <input type="file" id="ui_fotos" multiple class="hidden" accept="image/*">
                            <input type="file" id="input_fotos" name="fotos[]" multiple class="hidden">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- BOTONES FINALES --}}
        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-4">
            <a href="{{ route('mantenimientos.index') }}"
                class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                Regresar
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow active:scale-[0.98] transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                Guardar Cambios
            </button>
        </div>

    </form>
</div>

<form id="form_eliminar_foto" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAI = document.getElementById('btnAI');
        const panelAI = document.getElementById('panelAI');
        const iaResultado = document.getElementById('iaResultado');
        const diagnostico = document.getElementById('diagnostico');
        const btnCopiar = document.getElementById('btnCopiarAI');

        const descripcionProblema = @json($mantenimiento->descripcion);

        // Lógica de Consulta a la Inteligencia Artificial (IA) para Diagnóstico Predictivo
        btnAI.addEventListener('click', async function() {
            btnAI.disabled = true;
            const originalHTML = btnAI.innerHTML;
            // Mostrar animación de cargando en el botón
            btnAI.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg> Consultando...`;
            btnAI.classList.add('opacity-80', 'cursor-not-allowed');
            panelAI.classList.add('hidden');
            iaResultado.innerHTML = '';

            try {
                // Hacer petición POST a la API local de sugerencias de IA
                const res = await fetch('{{ route("ai.diagnosticar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        descripcion: descripcionProblema
                    }),
                });

                const data = await res.json();
                // Si todo sale bien, procesar el texto markdown de la respuesta y mostrarlo
                if (!data.error) parseAIDiagnostico(data.diagnostico);
                panelAI.classList.remove('hidden');

            } catch (err) {} finally {
                // Habilitar botón de IA de nuevo
                btnAI.disabled = false;
                btnAI.innerHTML = originalHTML;
                btnAI.classList.remove('opacity-80', 'cursor-not-allowed');
            }
        });

        function parseAIDiagnostico(text) {
            let html = '';
            let fullDiagnostico = '';
            let urgencia = 'Bajo';

            const sections = {
                'diagnostico': '',
                'soluciones': '',
                'componentes': '',
                'urgencia': ''
            };

            let currentSection = '';
            const lines = text.split('\n');

            lines.forEach(line => {
                const cleanLine = line.trim();
                if (!cleanLine) return;

                const normalized = cleanLine.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "");

                if (normalized.includes('diagnostico probable')) {
                    currentSection = 'diagnostico';
                } else if (normalized.includes('nivel de urgencia')) {
                    currentSection = 'urgencia';
                    sections['urgencia'] = cleanLine.split(':')[1] || '';
                } else if (normalized.includes('posibles soluciones')) {
                    currentSection = 'soluciones';
                } else if (normalized.includes('componentes sugeridos')) {
                    currentSection = 'componentes';
                } else if (currentSection && currentSection !== 'urgencia') {
                    sections[currentSection] += line + '\n';
                }
            });

            if (sections['diagnostico']) {
                fullDiagnostico = sections['diagnostico'].replace(/^- /gm, '').trim();
                html += `
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-150 dark:border-gray-700/60 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <h4 class="text-xs font-semibold text-blue-600 dark:text-blue-400">Diagnóstico Técnico</h4>
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">${sections['diagnostico'].replace(/\n/g, '<br>')}</div>
                </div>`;
            }

            if (sections['soluciones']) {
                html += `
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-150 dark:border-gray-700/60 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <h4 class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Plan de Acción</h4>
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">${sections['soluciones'].replace(/\n/g, '<br>')}</div>
                </div>`;
            }

            if (sections['componentes']) {
                html += `
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-150 dark:border-gray-700/60 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <h4 class="text-xs font-semibold text-amber-600 dark:text-amber-400">Repuestos Sugeridos</h4>
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">${sections['componentes'].replace(/\n/g, '<br>')}</div>
                </div>`;
            }

            urgencia = (sections['urgencia'] || '').replace(/\[|\]/g, '').trim() || 'Bajo';
            const urgColor = urgencia.includes('Alto') ? 'text-red-650 bg-red-50 border-red-200 dark:bg-red-950/30 dark:border-red-800 dark:text-red-400' :
                (urgencia.includes('Medio') ? 'text-amber-650 bg-amber-50 border-amber-200 dark:bg-amber-950/30 dark:border-amber-800 dark:text-amber-400' : 'text-emerald-650 bg-emerald-50 border-emerald-200 dark:bg-emerald-950/30 dark:border-emerald-800 dark:text-emerald-400');

            html += `
            <div class="col-span-full py-3 px-4 rounded-lg border ${urgColor} flex items-center justify-between">
                <span class="text-xs font-medium">Prioridad de Atención Recomendada</span>
                <span class="text-xs font-semibold">${urgencia}</span>
            </div>`;

            iaResultado.innerHTML = html;
            window.currentAIDiagnostico = fullDiagnostico;
        }

        btnCopiar.addEventListener('click', function() {
            if (window.currentAIDiagnostico) {
                diagnostico.value = window.currentAIDiagnostico;
                diagnostico.focus();

                // Visual feedback
                const originalHTML = btnCopiar.innerHTML;
                btnCopiar.innerHTML = `
                <svg class="w-3.5 h-3.5 text-emerald-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <span>¡Copiado!</span>
            `;
                btnCopiar.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                btnCopiar.classList.add('bg-emerald-600', 'hover:bg-emerald-700');

                setTimeout(() => {
                    btnCopiar.innerHTML = originalHTML;
                    btnCopiar.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                    btnCopiar.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                }, 2000);
            }
        });

        // Prevenir múltiple envío del formulario
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    setTimeout(() => {
                        btn.disabled = true;
                        btn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg> Guardando...`;
                        btn.classList.add('opacity-75', 'cursor-not-allowed');
                    }, 10);
                }
            });
        }

        // Inicializar estado seleccionado al cargar la página
        const initialStatus = document.getElementById('input_estado').value;
        if (initialStatus) {
            selectStatus(initialStatus);
        }

        // Actualizar dinámicamente el color del contenedor de cada componente al cambiar el select
        const componentSelects = document.querySelectorAll('select[name^="componentes"]');
        componentSelects.forEach(select => {
            select.addEventListener('change', function() {
                const container = this.closest('.rounded-xl');
                const dot = container.querySelector('.w-2.h-2');
                const val = this.value;

                // Limpiar clases anteriores del contenedor y del dot
                container.className = 'flex items-center justify-between px-4 py-3 border rounded-xl transition-all duration-200';
                dot.className = 'w-2 h-2 rounded-full';

                if (val === 'bueno') {
                    container.classList.add('bg-emerald-50/30', 'border-emerald-100/70', 'dark:bg-emerald-950/10', 'dark:border-emerald-900/30');
                    dot.classList.add('bg-emerald-500');
                } else if (val === 'regular') {
                    container.classList.add('bg-amber-50/30', 'border-amber-100/70', 'dark:bg-amber-950/10', 'dark:border-amber-900/30');
                    dot.classList.add('bg-amber-500');
                } else if (val === 'malo') {
                    container.classList.add('bg-red-50/30', 'border-red-100/70', 'dark:bg-red-950/10', 'dark:border-red-900/30');
                    dot.classList.add('bg-red-500');
                } else if (val === 'reemplazado') {
                    container.classList.add('bg-blue-50/30', 'border-blue-100/70', 'dark:bg-blue-950/10', 'dark:border-blue-900/30');
                    dot.classList.add('bg-blue-500');
                } else {
                    container.classList.add('bg-gray-50/50', 'border-gray-200', 'dark:bg-gray-800/30', 'dark:border-gray-750');
                    dot.classList.add('bg-gray-400');
                }
            });
        });
        
        // Lógica de carga, previsualización y cancelación de nuevas imágenes de evidencias
        const uiFotos = document.getElementById('ui_fotos');
        const inputFotos = document.getElementById('input_fotos');
        const previewContainer = document.getElementById('preview_fotos_container');
        const badgeCount = document.getElementById('badge_fotos_count');
        const existingCount = {{ $mantenimiento->fotos->count() }};

        if (uiFotos && previewContainer && inputFotos) {
            const label = uiFotos.closest('label');
            let selectedFiles = []; // Almacena en memoria los archivos cargados temporalmente

            // Se ejecuta al elegir una o varias imágenes de la ventana de diálogo
            uiFotos.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    Array.from(this.files).forEach(file => {
                        selectedFiles.push(file);
                    });
                    this.value = ''; // Permite volver a seleccionar el mismo archivo si es removido
                    renderPreviews();
                }
            });

            // Regenera la vista previa HTML de todas las imágenes cargadas temporalmente
            function renderPreviews() {
                // Eliminar vistas previas temporales anteriores para evitar duplicados
                const tempPreviews = previewContainer.querySelectorAll('.temp-preview');
                tempPreviews.forEach(el => el.remove());

                // Actualizar contador visual de evidencias en tiempo real
                if (badgeCount) {
                    badgeCount.textContent = existingCount + selectedFiles.length;
                }

                // Sincronizar archivos seleccionados con el input real oculto que se envía al servidor
                const dataTransfer = new DataTransfer();
                
                selectedFiles.forEach((file, index) => {
                    dataTransfer.items.add(file); // Añadir archivo al cargador maestro de datos
                    
                    // Crear previsualización en imagen
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'aspect-square rounded-xl border border-dashed border-indigo-400 overflow-hidden relative shadow-sm temp-preview group';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover">
                            <span class="absolute bottom-2 left-2 px-1.5 py-0.5 rounded bg-indigo-650 text-white text-[9px] font-bold">Por subir</span>
                            <button type="button" class="absolute top-2 right-2 p-1.5 rounded-lg bg-red-600/90 hover:bg-red-700 text-white opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-md remove-btn">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        `;

                        // Escuchar el clic para eliminar la imagen antes de subirla
                        div.querySelector('.remove-btn').addEventListener('click', function(ev) {
                            ev.preventDefault();
                            ev.stopPropagation();
                            selectedFiles.splice(index, 1); // Remover del array temporal
                            renderPreviews(); // Re-renderizar previews
                        });

                        previewContainer.insertBefore(div, label);
                    };
                    reader.readAsDataURL(file);
                });

                // Asignar el DataTransfer con los archivos limpios al input de archivo real
                inputFotos.files = dataTransfer.files;
            }
        }
    });

    function selectStatus(status) {
        document.getElementById('input_estado').value = status;
        const btns = document.querySelectorAll('.status-btn');

        // Reset all buttons
        btns.forEach(btn => {
            btn.className = 'status-btn flex flex-col items-center justify-center py-3.5 rounded-xl border transition-all duration-200 text-xs font-semibold border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-750 dark:hover:text-gray-200 dark:hover:border-gray-600 active:scale-[0.98]';
        });

        // Apply active styles
        const selectedBtn = document.getElementById('btn_' + status);
        if (status === 'pendiente') {
            selectedBtn.className = 'status-btn flex flex-col items-center justify-center py-3.5 rounded-xl border transition-all duration-200 text-xs font-bold border-amber-400 bg-amber-50/70 text-amber-800 dark:bg-amber-950/20 dark:border-amber-700 dark:text-amber-400 shadow-md shadow-amber-500/5 ring-1 ring-amber-400/30';
        } else if (status === 'en_proceso') {
            selectedBtn.className = 'status-btn flex flex-col items-center justify-center py-3.5 rounded-xl border transition-all duration-200 text-xs font-bold border-blue-400 bg-blue-50/70 text-blue-800 dark:bg-blue-950/20 dark:border-blue-700 dark:text-blue-400 shadow-md shadow-blue-500/5 ring-1 ring-blue-400/30';
        } else if (status === 'finalizado') {
            selectedBtn.className = 'status-btn flex flex-col items-center justify-center py-3.5 rounded-xl border transition-all duration-200 text-xs font-bold border-emerald-400 bg-emerald-50/70 text-emerald-800 dark:bg-emerald-950/20 dark:border-emerald-700 dark:text-emerald-400 shadow-md shadow-emerald-500/5 ring-1 ring-emerald-400/30';
        }
    }

    function eliminarFotoExistente(id) {
        if (confirm('¿Estás seguro de que deseas eliminar permanentemente esta imagen de evidencia?')) {
            const form = document.getElementById('form_eliminar_foto');
            form.action = "{{ url('mantenimientos/fotos') }}/" + id;
            form.submit();
        }
    }
</script>
@endpush
@endsection
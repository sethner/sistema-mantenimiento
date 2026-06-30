@extends('layouts.app')

@section('header')
<div class="flex items-center gap-3">
    <a href="{{ route('equipos.index') }}" class="group p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow transition-all duration-200">
        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Registrar Nuevo Equipo</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Complete la información para agregar un nuevo equipo al sistema.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">

    <form action="{{ route('equipos.store') }}" method="POST"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-150 dark:border-gray-700/60 overflow-hidden transition-all duration-300">
        @csrf

        <!-- Línea superior estética de gradiente -->
        <div class="h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

        {{-- HEADER CARD --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/50">
            <h3 class="text-gray-900 dark:text-white font-bold text-lg">Datos del Equipo</h3>
        </div>

        <div class="p-6 space-y-8">

            {{-- INFORMACIÓN BÁSICA --}}
            <div>
                <h4 class="text-sm font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Información Básica</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Código --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Código Patrimonial</label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" required
                            placeholder="Ej. AIP-01"
                            class="w-full px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-650 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm shadow-sm">
                        @error('codigo') 
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nombre del Equipo</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required 
                            placeholder="Ej. PC-LAB-01"
                            class="w-full px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-650 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm shadow-sm">
                        @error('nombre') 
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                </div>
            </div>

            {{-- DETALLES TÉCNICOS --}}
            <div>
                <h4 class="text-sm font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Detalles Técnicos</h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Tipo --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Tipo de Equipo</label>
                        <div class="relative">
                            <select name="tipo_id" required
                                class="w-full appearance-none px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-650 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm shadow-sm transition-all duration-200">
                                <option value="" disabled selected>Seleccione un tipo</option>
                                @foreach($tipos as $t)
                                    <option value="{{ $t->id }}" {{ old('tipo_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Marca --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Marca</label>
                        <input type="text" name="marca" value="{{ old('marca') }}"
                            placeholder="Ej. Dell, HP"
                            class="w-full px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-650 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm shadow-sm">
                    </div>

                    {{-- Modelo --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Modelo</label>
                        <input type="text" name="modelo" value="{{ old('modelo') }}"
                            placeholder="Ej. Optiplex 7050"
                            class="w-full px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-650 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm shadow-sm">
                    </div>

                    {{-- Frecuencia --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 font-medium">
                            Frecuencia (meses)
                        </label>
                        <input type="number" name="frecuencia_mantenimiento"
                            value="{{ old('frecuencia_mantenimiento', 6) }}"
                            min="1" max="24"
                            class="w-full px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-655 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm shadow-sm">
                        @error('frecuencia_mantenimiento') 
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p> 
                        @enderror
                    </div>

                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="px-6 py-5 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-150 dark:border-gray-700/60 flex justify-between items-center">

            <a href="{{ route('equipos.index') }}"
                class="px-5 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-75 transition-all duration-200 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Volver</span>
            </a>

            <button type="submit"
                class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-97 transition-all duration-200 text-sm flex items-center gap-2">
                <span>Guardar Equipo</span>
            </button>

        </div>
    </form>
</div>

<!-- SCRIPT DE ENVÍO -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endsection
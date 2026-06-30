@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('mantenimientos.index') }}" class="group p-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow transition-all duration-200">
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                Nuevo Mantenimiento
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Registre una nueva tarea o correctivo en el sistema.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto pb-10">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-150 dark:border-gray-700/60 overflow-hidden transition-all duration-300">
        
        <!-- Línea superior estética de gradiente -->
        <div class="h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

        <div class="p-6 sm:p-8">
            @if ($errors->any())
                <div class="flex gap-3 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-xl text-sm text-red-700 dark:text-red-300 mb-6 animate-pulse">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <span class="font-bold">Error en validación:</span> {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form action="{{ route('mantenimientos.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- EQUIPO -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Equipo
                        </label>
                        <div class="relative">
                            <select name="equipo_id"
                                class="w-full appearance-none border border-gray-200 dark:border-gray-650 rounded-xl px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200">
                                @foreach($equipos as $e)
                                    <option value="{{ $e->id }}" {{ old('equipo_id', request('equipo_id')) == $e->id ? 'selected' : '' }}>
                                        [{{ $e->codigo }}] - {{ $e->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-550">Selecciona el equipo por su código único</p>
                    </div>

                    <!-- TECNICO -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Técnico
                        </label>
                        @if(auth()->user()->hasRole('tecnico'))
                            <div class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 bg-gray-100/80 dark:bg-gray-750/80 text-gray-500 dark:text-gray-400 cursor-not-allowed flex items-center justify-between">
                                <span>{{ auth()->user()->name }}</span>
                                <span class="text-[9px] uppercase tracking-wider font-extrabold bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-md">Solo Lectura</span>
                            </div>
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        @else
                            <div class="relative">
                                <select name="user_id"
                                    class="w-full appearance-none border border-gray-200 dark:border-gray-650 rounded-xl px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200">
                                    @foreach($usuarios as $u)
                                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }} (Técnico)
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- TIPO -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Tipo de mantenimiento
                        </label>
                        <div class="relative">
                            <select id="tipo" name="tipo"
                                class="w-full appearance-none border border-gray-200 dark:border-gray-650 rounded-xl px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200">
                                <option value="correctivo" {{ old('tipo') == 'correctivo' ? 'selected' : '' }}>Correctivo</option>
                                <option value="preventivo" {{ old('tipo') == 'preventivo' ? 'selected' : '' }}>Preventivo</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- DESCRIPCION DINAMICA -->
                <div class="space-y-2">
                    <label id="labelDescripcion" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Descripción del problema
                    </label>
                    <textarea name="descripcion" rows="4"
                        placeholder="Describa el problema o actividad..."
                        class="w-full border border-gray-200 dark:border-gray-650 rounded-xl px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 resize-y">{{ old('descripcion') }}</textarea>
                    <p id="hintDescripcion" class="text-xs text-gray-400 dark:text-gray-555">El técnico recibirá esta descripción para diagnosticar el equipo.</p>
                </div>

                <!-- FECHAS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="space-y-2">
                        <label id="labelFecha" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Fecha
                        </label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}"
                            class="w-full border border-gray-200 dark:border-gray-655 rounded-xl px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200">
                    </div>

                    <!-- PROXIMA FECHA -->
                    <div id="campoProxima" class="space-y-2">
                        <label id="labelProxima" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Próxima fecha (solo preventivo)
                        </label>
                        <input type="date" name="proxima_fecha" value="{{ old('proxima_fecha') }}"
                            class="w-full border border-gray-200 dark:border-gray-655 rounded-xl px-4 py-3 bg-gray-50/50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200">
                    </div>

                </div>

                <!-- ACCIONES -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('mantenimientos.index') }}"
                       class="px-5 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-75 transition-all duration-200 text-sm">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-97 transition-all duration-200 text-sm flex items-center gap-2">
                        <span>Guardar Mantenimiento</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- SCRIPT DINAMICO -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipo = document.getElementById('tipo');
    const campoProxima = document.getElementById('campoProxima');
    const labelDescripcion = document.getElementById('labelDescripcion');
    const hintDescripcion = document.getElementById('hintDescripcion');
    const labelFecha = document.getElementById('labelFecha');
    const labelProxima = document.getElementById('labelProxima');

    function updateForm() {
        if (tipo.value === 'preventivo') {
            campoProxima.style.display = 'block';
            labelDescripcion.textContent = 'Actividad a realizar';
            hintDescripcion.textContent = 'Especifique las tareas de limpieza o revisión programadas.';
            if (labelFecha) labelFecha.textContent = 'Fecha programada';
            if (labelProxima) labelProxima.textContent = 'Fecha de ejecución';
        } else {
            campoProxima.style.display = 'none';
            labelDescripcion.textContent = 'Descripción del problema';
            hintDescripcion.textContent = 'El técnico recibirá esta descripción para diagnosticar el equipo.';
            if (labelFecha) labelFecha.textContent = 'Fecha de ejecución';
        }
    }

    tipo.addEventListener('change', updateForm);
    updateForm();

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
});
</script>
@endsection
@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <x-heroicon-o-wrench-screwdriver class="w-6 h-6 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                {{ auth()->user()->hasRole('tecnico') ? 'Mis Mantenimientos Asignados' : 'Gestión de Mantenimientos' }}
            </h2>
            <p class="text-sm text-gray-500">
                {{ auth()->user()->hasRole('tecnico') ? 'Tareas pendientes por ejecutar' : 'Control, prevención y seguimiento de equipos AIP' }}
            </p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
        </div>

        @if(!auth()->user()->hasRole('tecnico'))
        <a href="{{ route('mantenimientos.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-5 py-2.5 rounded-xl shadow-md hover:scale-105 hover:shadow-lg transition-all duration-200">
            + Nuevo Mantenimiento
        </a>
        @endif
    </div>

    @include('components.session.success')


    <!-- FILTROS Y BÚSQUEDA -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('mantenimientos.index') }}" method="GET" class="w-full md:w-1/3 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-5 h-5" />
            </span>
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Buscar por equipo o código..."
                class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
        </form>
        
        <div class="flex items-center gap-2">
            @if(request('search'))
                <a href="{{ route('mantenimientos.index') }}" class="text-xs font-semibold text-gray-500 hover:text-indigo-600 transition-colors">
                    Limpiar Filtros
                </a>
            @endif
        </div>
    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

        <table class="w-full text-sm text-left">
            
            <!-- HEADER -->
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-5 py-4">Equipo</th>
                    @if(auth()->user()->hasRole('tecnico'))
                        <th class="px-5 py-4">Problema</th>
                        <th class="px-5 py-4">Estado</th>
                        <th class="px-5 py-4">Costo</th>
                        <th class="px-5 py-4 text-center">Acción</th>
                    @else
                        <th class="px-5 py-4">Tipo</th>
                        <th class="px-5 py-4">Técnico</th>
                        <th class="px-5 py-4 text-center">Fecha/Prox.</th>
                        <th class="px-5 py-4">Estado</th>
                        <th class="px-5 py-4 text-right">Costo</th>
                        <th class="px-5 py-4 text-center">Acciones</th>
                    @endif
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="divide-y divide-gray-100">

                @forelse($mantenimientos as $m)

                    @php
                        $estadoColor = match($m->estado) {
                            'pendiente' => 'bg-yellow-100 text-yellow-700',
                            'en_proceso' => 'bg-blue-100 text-blue-700',
                            'finalizado' => 'bg-green-100 text-green-700',
                            default => 'bg-gray-100 text-gray-500',
                        };

                        $tipoColor = ($m->tipo ?? 'correctivo') === 'preventivo'
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-red-100 text-red-700';
                    @endphp

                    <tr class="hover:bg-gray-50 transition duration-150">

                        <!-- ✅ EQUIPO MEJORADO -->
                        <td class="px-5 py-4">
                            <div>
                                <p class="font-semibold text-gray-800">
                                    {{ $m->equipo->nombre ?? '-' }}
                                </p>

                                <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                    {{ $m->equipo->codigo ?? '' }}
                                </span>
                            </div>
                        </td>

                        @if(auth()->user()->hasRole('tecnico'))
                            <!-- PROBLEMA -->
                            <td class="px-5 py-4 text-gray-600 italic">
                                "{{ Str::limit($m->descripcion, 50) }}"
                            </td>

                            <!-- ESTADO -->
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 text-xs rounded-full font-semibold {{ $estadoColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $m->estado)) }}
                                </span>
                            </td>

                            <!-- COSTO -->
                            <td class="px-5 py-4 font-bold text-gray-700">
                                S/ {{ number_format($m->costo, 2) }}
                            </td>

                            <!-- ACCIÓN TÉCNICO -->
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center items-center gap-2">
                                    @if($m->estado === 'pendiente')
                                        <form action="{{ route('mantenimientos.iniciar', $m) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold text-xs shadow-sm transition-all whitespace-nowrap">
                                                ▶ INICIAR
                                            </button>
                                        </form>
                                    @elseif($m->estado === 'en_proceso')
                                        <a href="{{ route('mantenimientos.show', $m) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-bold text-xs shadow-sm transition-all whitespace-nowrap">
                                            🛠 CONTINUAR
                                        </a>
                                    @endif
                                </div>
                            </td>
                        @else
                            <!-- TIPO -->
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $tipoColor }}">
                                    {{ ucfirst($m->tipo ?? 'correctivo') }}
                                </span>
                            </td>

                            <!-- TECNICO -->
                            <td class="px-5 py-4 text-gray-600">
                                {{ $m->usuario->name ?? '-' }}
                            </td>

                            <!-- FECHAS -->
                            <td class="px-5 py-4 text-center">
                                @if(($m->tipo ?? 'correctivo') === 'preventivo')
                                    <div class="text-gray-600 font-medium" title="Fecha programada">Prog: {{ optional($m->fecha)->format('d/m/Y') ?? $m->fecha }}</div>
                                    <div class="text-[10px] text-gray-400" title="Fecha de ejecución">Ejec: {{ optional($m->proxima_fecha)->format('d/m/Y') ?? '-' }}</div>
                                @else
                                    <div class="text-gray-600 font-medium" title="Fecha de ejecución">Ejec: {{ optional($m->fecha)->format('d/m/Y') ?? $m->fecha }}</div>
                                @endif
                            </td>

                            <!-- ESTADO -->
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 text-xs rounded-full font-semibold {{ $estadoColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $m->estado)) }}
                                </span>
                            </td>

                            <!-- COSTO -->
                            <td class="px-5 py-4 text-right">
                                <span class="font-bold text-indigo-600">S/ {{ number_format($m->costo, 2) }}</span>
                            </td>

                            <!-- ACCIONES ADMIN -->
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('mantenimientos.show', $m) }}"
                                       class="inline-flex items-center justify-center p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                       title="Ver Mantenimiento">
                                        <x-icons.eye />
                                    </a>

                                    <a href="{{ route('mantenimientos.edit', $m) }}"
                                       class="inline-flex items-center justify-center p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                       title="Editar Mantenimiento">
                                        <x-icons.edit />
                                    </a>

                                    <form action="{{ route('mantenimientos.destroy', $m) }}" method="POST" class="inline-flex m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('¿Seguro que desea eliminar este mantenimiento?')"
                                            class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Eliminar Mantenimiento">
                                            <x-icons.delete />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif

                    </tr>

                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->hasRole('tecnico') ? '5' : '7' }}" class="text-center py-12 text-gray-400 text-sm">
                            🚫 No hay mantenimientos {{ auth()->user()->hasRole('tecnico') ? 'asignados actualmente' : 'registrados' }}
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    <div class="mt-4">
        {{ $mantenimientos->links() }}
    </div>
</div>
@endsection
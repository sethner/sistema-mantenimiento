@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <x-heroicon-o-computer-desktop class="w-6 h-6 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Gestión de Equipos</h2>
            <p class="text-sm text-gray-500">Administra los dispositivos tecnológicos y su estado operativo.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    @include('components.session.success')



    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">Lista de Equipos</h3>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <form action="{{ route('equipos.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Buscar por nombre o código..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>

                <select name="tipo_id" onchange="this.form.submit()"
                    class="block w-full sm:w-48 py-2 px-3 border border-gray-300 bg-white rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('tipo_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>

                <select name="estado" onchange="this.form.submit()"
                    class="block w-full sm:w-48 py-2 px-3 border border-gray-300 bg-white rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $estado)) }}
                        </option>
                    @endforeach
                </select>
                
                @if(request('search') || request('estado') || request('tipo_id'))
                    <a href="{{ route('equipos.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center justify-center p-2">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </a>
                @endif
            </form>

            <a href="{{ route('equipos.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <x-heroicon-s-plus class="w-4 h-4" />
                Nuevo Equipo
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Código</th>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($equipos as $equipo)
                    @php
                        $estadoColor = match($equipo->estado) {
                            'operativo' => 'bg-green-100 text-green-700',
                            'en_mantenimiento' => 'bg-blue-100 text-blue-700',
                            'con_falla' => 'bg-red-100 text-red-700',
                            'dado_de_baja' => 'bg-gray-200 text-gray-700',
                            default => 'bg-gray-100 text-gray-500',
                        };
                    @endphp

                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-700">{{ $equipo->codigo }}</td>
                        <td class="px-4 py-3">{{ $equipo->nombre }}</td>
                        <td class="px-4 py-3">
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                                {{ $equipo->tipo->nombre ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="{{ $estadoColor }} px-2 py-1 rounded text-xs">
                                {{ ucfirst(str_replace('_', ' ', $equipo->estado)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('equipos.show', $equipo) }}" 
                                   class="inline-flex items-center justify-center p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" 
                                   title="Ver Detalle del Equipo">
                                    <x-icons.eye />
                                </a>

                                <a href="{{ route('equipos.edit', $equipo) }}"
                                   class="inline-flex items-center justify-center p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                   title="Editar Equipo">
                                    <x-icons.edit />
                                </a>

                                <form action="{{ route('equipos.destroy', $equipo) }}" method="POST" class="inline-flex m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('¿Seguro que desea eliminar este equipo?')"
                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Eliminar Equipo">
                                        <x-icons.delete />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500">
                            No hay equipos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $equipos->links() }}
    </div>
</div>
@endsection

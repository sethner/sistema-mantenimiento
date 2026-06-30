@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <x-heroicon-o-queue-list class="w-6 h-6 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Tipos de Equipos</h2>
            <p class="text-sm text-gray-500">Administra los tipos de equipos del sistema.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    @include('components.session.success')



    <div class="flex justify-between items-center">
        <div>
        </div>

        <a href="{{ route('tipos-equipos.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            + Nuevo Tipo
        </a>
    </div>

    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Tipo de Equipo</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($tipos as $t)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($t->imagen)
                                    <img src="{{ asset($t->imagen) }}" alt="{{ $t->nombre }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold border border-indigo-200">
                                        {{ substr($t->nombre, 0, 1) }}
                                    </div>
                                @endif
                                <span class="font-medium text-gray-700">{{ $t->nombre }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('tipos-equipos.edit', $t) }}"
                                   class="inline-flex items-center justify-center p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                   title="Editar Tipo de Equipo">
                                    <x-icons.edit />
                                </a>

                                <form action="{{ route('tipos-equipos.destroy', $t) }}" method="POST" class="inline-flex m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('¿Seguro que desea eliminar este tipo de equipo?')"
                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Eliminar Tipo de Equipo">
                                        <x-icons.delete />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-6 text-gray-500">
                            No hay tipos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

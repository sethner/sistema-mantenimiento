@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <x-heroicon-o-tag class="w-6 h-6 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Categorías de Componentes</h2>
            <p class="text-sm text-gray-500">Administra las categorías del sistema.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
        </div>

        <a href="{{ route('categorias.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            + Nueva Categoría
        </a>
    </div>

    @include('components.session.success')


    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($categorias as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $c->nombre }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('categorias.edit', $c) }}"
                                   class="inline-flex items-center justify-center p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                   title="Editar Categoría">
                                    <x-icons.edit />
                                </a>

                                <form action="{{ route('categorias.destroy', $c) }}" method="POST" class="inline-flex m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('¿Seguro que desea eliminar esta categoría?')"
                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Eliminar Categoría">
                                        <x-icons.delete />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center py-6 text-gray-400">
                            No hay categorías registradas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">Editar Categoría</h2>

    <div class="bg-white p-6 rounded-xl shadow border">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm mb-1">Nombre</label>
                <input type="text" name="nombre"
                       value="{{ old('nombre', $categoria->nombre) }}"
                       class="w-full border px-3 py-2 rounded">
                @error('nombre')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('categorias.index') }}" class="px-4 py-2 border rounded">
                    Cancelar
                </a>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

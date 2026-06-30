@extends('layouts.app')

@section('header')
<h2 class="text-2xl font-semibold text-gray-800">Editar Tipo de Equipo</h2>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white p-6 rounded-xl shadow border">
        <form action="{{ route('tipos-equipos.update', $tipo) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text" name="nombre"
                       value="{{ old('nombre', $tipo->nombre) }}"
                       class="w-full border px-3 py-2 rounded-lg">
                @error('nombre')
                    <p class="text-red-500 text-xs">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2 text-gray-700">Imagen Representativa</label>
                
                @if($tipo->imagen)
                    <div class="mb-3">
                        <img src="{{ asset($tipo->imagen) }}" alt="{{ $tipo->nombre }}" class="w-24 h-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Imagen actual</p>
                    </div>
                @endif
                
                <input type="file" name="imagen" accept="image/*"
                       class="w-full border px-3 py-2 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-1">Sube una nueva imagen solo si deseas reemplazar la actual.</p>
                @error('imagen')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('tipos-equipos.index') }}" class="px-4 py-2 border rounded-lg">
                    Cancelar
                </a>
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

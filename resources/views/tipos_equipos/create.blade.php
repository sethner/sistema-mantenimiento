@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800">Nuevo Tipo de Equipo</h2>
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white p-6 rounded-xl shadow border space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-700">Registrar Tipo</h3>
            <p class="text-sm text-gray-500">Define una categoría de equipo (PC, Laptop, etc.)</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tipos-equipos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Nombre del Tipo</label>
                <input type="text" name="nombre"
                       value="{{ old('nombre') }}"
                       placeholder="Ej: PC, Laptop, Impresora, Proyector"
                       class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                @error('nombre')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700">Imagen Representativa</label>
                <input type="file" name="imagen" accept="image/*"
                       class="w-full border px-3 py-2 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-1">Formatos soportados: JPG, PNG, WEBP. Tamaño máximo: 2MB.</p>
                @error('imagen')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('tipos-equipos.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

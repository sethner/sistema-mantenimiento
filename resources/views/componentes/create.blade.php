@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Nuevo Componente</h2>
        <p class="text-sm text-gray-500">Define un componente base para un tipo de equipo</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-2xl shadow border">
        <form action="{{ route('componentes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Tipo de Equipo</label>
                <select name="tipo_id" required
                    class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccione tipo</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t->id }}" {{ old('tipo_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nombre del componente</label>
                <input type="text" name="nombre"
                    value="{{ old('nombre') }}"
                    placeholder="Ej: RAM, Disco duro, Monitor"
                    required
                    class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500">
                @error('nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Categoría</label>
                <select name="categoria_id" required
                    class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Seleccione categoría</option>
                    @foreach($categorias as $c)
                        <option value="{{ $c->id }}" {{ old('categoria_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('categoria_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Imagen (Opcional)</label>
                <input type="file" name="imagen" accept="image/*"
                    class="w-full border px-3 py-2 rounded-lg focus:ring-2 focus:ring-blue-500">
                @error('imagen') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('componentes.index') }}"
                   class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                    Cancelar
                </a>

                <button class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

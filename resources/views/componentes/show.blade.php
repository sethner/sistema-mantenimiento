@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detalle del Componente</h2>
            <p class="text-sm text-gray-500">Información y equipos asociados</p>
        </div>

        <a href="{{ route('componentes.index') }}"
           class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50 text-sm font-medium shadow-sm transition">
            ← Volver a Componentes
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col md:flex-row">
            <div class="md:w-1/3 bg-gray-50 p-8 border-r border-gray-100 flex flex-col items-center justify-center">
                @if($componente->imagen)
                    <img src="{{ asset($componente->imagen) }}" alt="{{ $componente->nombre }}" class="w-48 h-48 object-contain rounded-xl bg-white shadow-sm border border-gray-100 mb-4 p-2">
                @else
                    <div class="w-48 h-48 bg-white border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 mb-4 shadow-sm">
                        <x-heroicon-o-cpu-chip class="w-16 h-16 mb-2 text-gray-300" />
                        <span class="text-xs font-medium uppercase tracking-widest">Sin Imagen</span>
                    </div>
                @endif
            </div>
            
            <div class="md:w-2/3 p-8">
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">{{ $componente->nombre }}</h1>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold uppercase tracking-wider rounded-md border border-indigo-100">
                                {{ \App\Models\TipoEquipo::where('id', $componente->tipo_id)->value('nombre') ?? 'Sin Tipo' }}
                            </span>
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold uppercase tracking-wider rounded-md border border-gray-200">
                                {{ optional($componente->categoria)->nombre ?? 'Sin Categoría' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('componentes.edit', $componente) }}" class="p-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition" title="Editar">
                            <x-heroicon-s-pencil class="w-5 h-5" />
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 pt-6 border-t border-gray-100">
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Equipos Usando</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $componente->equipos ? $componente->equipos->count() : 0 }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Fecha de Registro</p>
                        <p class="text-lg font-bold text-gray-800">{{ $componente->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

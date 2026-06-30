@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <x-heroicon-o-cpu-chip class="w-6 h-6 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Componentes por Tipo</h2>
            <p class="text-sm text-gray-500">Define los componentes base de cada tipo de equipo.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
        </div>

        <a href="{{ route('componentes.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-5 py-2.5 rounded-xl shadow-md hover:scale-105 hover:shadow-lg transition-all duration-200">
            <x-heroicon-s-plus class="w-5 h-5" />
            Nuevo Componente
        </a>
    </div>

    @include('components.session.success')
    @include('components.session.error')


    <div class="space-y-6" x-data="{ activeGroup: '{{ \Illuminate\Support\Str::slug($componentes->groupBy(function($c) { return $c->tipo->nombre ?? \App\Models\TipoEquipo::where('id', $c->tipo_id)->value('nombre') ?? 'Sin Tipo'; })->keys()->first() ?? '') }}' }">
        @php
            $agrupados = $componentes->groupBy(function($c) {
                return $c->tipo->nombre ?? \App\Models\TipoEquipo::where('id', $c->tipo_id)->value('nombre') ?? 'Sin Tipo';
            });
        @endphp
        
        @forelse($agrupados as $tipoNombre => $grupo)
            @php
                $tipoLower = strtolower(trim($tipoNombre));
                $groupId = \Illuminate\Support\Str::slug($tipoNombre);
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <!-- Header del Grupo -->
                <div @click="activeGroup = activeGroup === '{{ $groupId }}' ? null : '{{ $groupId }}'" 
                     class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100 flex justify-between items-center cursor-pointer select-none">
                    <div class="flex items-center gap-3">
                        @php
                            $tipoModel = $grupo->first()->tipo ?? \App\Models\TipoEquipo::find($grupo->first()->tipo_id);
                        @endphp
                        @if($tipoModel && $tipoModel->imagen)
                            <img src="{{ asset($tipoModel->imagen) }}" alt="{{ $tipoNombre }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200 shadow-sm bg-white">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold border border-indigo-200 shadow-sm">
                                {{ substr($tipoNombre, 0, 1) }}
                            </div>
                        @endif
                        <h3 class="font-bold text-gray-800 text-lg uppercase tracking-wide">{{ $tipoNombre }}</h3>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1.5 rounded-full">
                            {{ $grupo->count() }} {{ $grupo->count() == 1 ? 'Componente' : 'Componentes' }}
                        </span>
                        <div class="text-gray-400 transition-transform duration-300" :class="activeGroup === '{{ $groupId }}' ? 'rotate-180' : ''">
                            <x-heroicon-o-chevron-down class="w-5 h-5" />
                        </div>
                    </div>
                </div>
                
                <!-- Lista de Componentes -->
                <div x-show="activeGroup === '{{ $groupId }}'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     style="display: none;"
                     class="divide-y divide-gray-50">
                    @foreach($grupo as $c)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-indigo-50/30 transition-colors group">
                            <div class="flex items-center gap-4">
                                @if($c->imagen)
                                    <img src="{{ asset($c->imagen) }}" alt="{{ $c->nombre }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shadow-sm group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-blue-100 text-indigo-600 font-bold text-lg rounded-xl flex items-center justify-center border border-indigo-200 shadow-sm uppercase group-hover:scale-105 transition-transform duration-300">
                                        {{ substr($c->nombre, 0, 1) }}
                                    </div>
                                @endif
                                
                                <div class="flex flex-col gap-1">
                                    <span class="font-bold text-gray-800 text-sm">{{ $c->nombre }}</span>
                                    @php
                                        $nombreCategoria = $c->categoria->nombre ?? 'Sin categoría';
                                        $color = match(strtolower($nombreCategoria)) {
                                            'interno' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'periferico' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'consumible' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 text-[10px] uppercase tracking-wider rounded-md font-bold border {{ $color }} w-fit">
                                        {{ $nombreCategoria }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-200">
                                <a href="{{ route('componentes.show', $c) }}" 
                                   class="p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white rounded-lg transition-colors shadow-sm" 
                                   title="Ver Detalles">
                                    <x-heroicon-s-eye class="w-4 h-4" />
                                </a>

                                <a href="{{ route('componentes.edit', $c) }}"
                                   class="p-2 text-amber-600 bg-amber-50 hover:bg-amber-500 hover:text-white rounded-lg transition-colors shadow-sm"
                                   title="Editar Componente">
                                    <x-heroicon-s-pencil-square class="w-4 h-4" />
                                </a>

                                <form action="{{ route('componentes.destroy', $c) }}" method="POST" class="inline-block m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('¿Seguro que desea eliminar este componente?')"
                                        class="p-2 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white rounded-lg transition-colors shadow-sm"
                                        title="Eliminar Componente">
                                        <x-heroicon-s-trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <x-heroicon-o-cpu-chip class="w-10 h-10 text-gray-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">No hay componentes</h3>
                <p class="text-gray-500 text-sm max-w-sm mb-6">Aún no has registrado ningún componente. Comienza agregando componentes a tu catálogo.</p>
                <a href="{{ route('componentes.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-medium shadow-md shadow-indigo-200 hover:bg-indigo-700 transition-colors">
                    <x-heroicon-s-plus class="w-5 h-5" />
                    Registrar el primero
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection

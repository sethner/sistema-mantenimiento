@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Carga de Trabajo de Técnicos</h2>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($tecnicos as $t)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 flex items-center gap-4 border-b border-gray-50">
                <img src="{{ $t['foto'] ? asset($t['foto']) : 'https://ui-avatars.com/api/?name='.urlencode($t['nombre']).'&background=eef2ff&color=4f46e5&size=128&bold=true' }}" 
                     class="w-14 h-14 rounded-2xl object-cover border-2 border-gray-100" alt="{{ $t['nombre'] }}">
                <div>
                    <h3 class="font-bold text-gray-900 leading-tight">{{ $t['nombre'] }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Técnico de Mantenimiento</p>
                </div>
            </div>
            
            <div class="p-6 flex-1 space-y-6">
                {{-- Barra de progreso total --}}
                <div>
                    <div class="flex items-center justify-between text-xs font-bold mb-2">
                        <span class="text-gray-500 uppercase tracking-wider">Mantenimientos Totales</span>
                        <span class="text-gray-900">{{ $t['total'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden flex">
                        @if($t['total'] > 0)
                            <div class="bg-emerald-500 h-full" style="width: {{ ($t['finalizados'] / $t['total']) * 100 }}%"></div>
                            <div class="bg-indigo-500 h-full" style="width: {{ ($t['en_proceso'] / $t['total']) * 100 }}%"></div>
                            <div class="bg-amber-500 h-full" style="width: {{ ($t['pendientes'] / $t['total']) * 100 }}%"></div>
                        @endif
                    </div>
                </div>

                {{-- Grid de stats --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pendientes</p>
                        <p class="text-lg font-bold text-amber-600">{{ $t['pendientes'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">En Proceso</p>
                        <p class="text-lg font-bold text-indigo-600">{{ $t['en_proceso'] }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Finalizados</p>
                        <p class="text-lg font-bold text-emerald-600">{{ $t['finalizados'] }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-2xl border border-red-100">
                        <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest mb-1">Vencidos</p>
                        <p class="text-lg font-bold text-red-600">{{ $t['vencidos'] }}</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-500">
                    {{ $t['total'] > 0 ? round(($t['finalizados'] / $t['total']) * 100) : 0 }}% de efectividad
                </span>
                <a href="{{ route('mantenimientos.index', ['tecnico' => $t['id']]) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Ver tareas →</a>
            </div>
        </div>
    @endforeach
</div>
@endsection

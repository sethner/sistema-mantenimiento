@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Perfil del Usuario</h2>
            <p class="text-sm text-gray-500">Información general y mantenimientos</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('usuarios.edit', $usuario) }}"
               class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm font-medium transition shadow-sm">
                Editar
            </a>
            <a href="{{ route('usuarios.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm transition">
                Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow border space-y-4">
            <div class="flex flex-col items-center gap-3">
                @if($usuario->foto)
                    <img src="{{ asset($usuario->foto) }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow"
                         alt="{{ $usuario->name }}">
                @else
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow">
                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                    </div>
                @endif

                <div class="text-center">
                    <h3 class="font-semibold text-lg text-gray-800">{{ $usuario->name }}</h3>
                    <p class="text-gray-500 text-sm">{{ $usuario->email }}</p>
                </div>

                @php
                    $rol = strtolower($usuario->roles->first()->nombre ?? 'sin rol');
                    $rolesUI = [
                        'administrador' => 'bg-blue-100 text-blue-700',
                        'tecnico' => 'bg-green-100 text-green-700',
                        'supervisor' => 'bg-purple-100 text-purple-700',
                    ];
                @endphp

                <span class="px-3 py-1 text-xs rounded-full font-medium {{ $rolesUI[$rol] ?? 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($rol) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-center pt-4 border-t">
                <div>
                    <p class="text-xl font-bold text-gray-800">{{ $mantenimientos->count() }}</p>
                    <p class="text-xs text-gray-500">Total</p>
                </div>

                <div>
                    <p class="text-xl font-bold text-green-600">
                        {{ $mantenimientos->where('estado', 'finalizado')->count() }}
                    </p>
                    <p class="text-xs text-gray-500">Finalizados</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl shadow border">
            <div class="flex justify-between items-center p-5 border-b">
                <h3 class="font-semibold text-gray-700">Historial de Mantenimientos</h3>
                <span class="text-xs text-gray-400">{{ $mantenimientos->count() }} registros</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Equipo</th>
                            <th class="px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($mantenimientos as $m)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-medium text-gray-700">
                                    {{ $m->equipo->nombre ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ optional($m->fecha)->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $estadoColor = match($m->estado) {
                                            'pendiente' => 'bg-yellow-100 text-yellow-700',
                                            'en_proceso' => 'bg-blue-100 text-blue-700',
                                            'finalizado' => 'bg-green-100 text-green-700',
                                            default => 'bg-gray-100 text-gray-500',
                                        };
                                    @endphp

                                    <span class="px-3 py-1 text-xs rounded-full font-medium {{ $estadoColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $m->estado)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400">
                                    No hay mantenimientos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('header')
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <x-heroicon-o-users class="w-6 h-6 text-white" />
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Gestión de Usuarios</h2>
            <p class="text-sm text-gray-500">Administra el acceso, credenciales y roles del personal del AIP.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- HEADER PREMIUM -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
        </div>

        <a href="{{ route('usuarios.create') }}"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-5 py-2.5 rounded-xl shadow-md hover:scale-105 hover:shadow-lg transition-all duration-200">
            <x-heroicon-s-user-plus class="w-5 h-5" />
            Nuevo Usuario
        </a>
    </div>

    @include('components.session.success')


    <!-- FILTROS Y BÚSQUEDA PREMIUM -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('usuarios.index') }}" method="GET" class="w-full md:flex-1 flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                </span>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Buscar por nombre o correo..."
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            </div>

            <div class="relative w-full md:w-48">
                <select name="rol" onchange="this.form.submit()"
                    class="block w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 outline-none appearance-none bg-white">
                    <option value="">Todos los roles</option>
                    <option value="administrador" {{ request('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="tecnico" {{ request('rol') == 'tecnico' ? 'selected' : '' }}>Técnico</option>
                    <option value="supervisor" {{ request('rol') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-400">
                    <x-heroicon-o-chevron-down class="w-4 h-4" />
                </div>
            </div>

            @if(request('search') || request('rol'))
                <a href="{{ route('usuarios.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-gray-500 hover:text-red-600 transition-colors">
                    LIMPIAR
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Usuario</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($usuarios as $u)
                    @php
                        $rol = strtolower($u->roles->first()->nombre ?? 'sin rol');
                        $rolesUI = [
                            'administrador' => 'bg-blue-100 text-blue-700',
                            'tecnico' => 'bg-green-100 text-green-700',
                            'supervisor' => 'bg-purple-100 text-purple-700',
                        ];
                    @endphp

                        <td class="px-4 py-3 flex items-center gap-3">
                            @if($u->foto && file_exists(public_path($u->foto)))
                                <img src="{{ asset($u->foto) }}" class="w-9 h-9 rounded-full object-cover" alt="{{ $u->name }}">
                            @else
                                <div class="w-9 h-9 bg-indigo-600 text-white flex items-center justify-center rounded-full text-sm font-bold shadow-sm">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                            @endif

                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800">{{ $u->name }}</span>
                                
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $rolesUI[$rol] ?? 'bg-gray-100 text-gray-500' }} px-2 py-1 rounded text-xs">
                                {{ ucfirst($rol) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('usuarios.edit', $u) }}"
                                   class="inline-flex items-center justify-center p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                   title="Editar Usuario">
                                    <x-icons.edit />
                                </a>

                                @if($rol !== 'administrador')
                                    <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="inline-flex m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('¿Seguro que desea eliminar este usuario?')"
                                            class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Eliminar Usuario">
                                            <x-icons.delete />
                                        </button>
                                    </form>
                                @else
                                    <div class="w-9 h-9"></div>
                                @endif

                                @if($rol === 'tecnico')
                                    <a href="{{ route('usuarios.show', $u) }}"
                                       class="inline-flex items-center justify-center p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                       title="Ver Perfil">
                                        <x-icons.eye />
                                    </a>
                                @else
                                    <div class="w-9"></div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            Usuario no encontrado
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $usuarios->links() }}
    </div>
</div>
@endsection

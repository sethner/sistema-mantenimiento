<aside class="w-68 bg-white text-gray-600 dark:bg-[#09090b] dark:text-gray-400 flex flex-col h-full border-r border-gray-200 dark:border-white/5 relative overflow-hidden transition-colors duration-300">
    
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-indigo-500/5 to-transparent pointer-events-none"></div>

    <!-- LOGO SECTION -->
    <div class="relative p-6 flex items-center gap-4 border-b border-gray-100 dark:border-b-white/5">
        @if($config->logo_path)
            <img src="{{ asset('storage/' . $config->logo_path) }}" class="w-10 h-10 rounded-xl object-contain shadow-lg" alt="Logo">
        @else
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        @endif
        <div>
            <p class="text-gray-900 dark:text-white font-bold text-base tracking-tight leading-none">{{ $config->nombre_institucion ?? 'Sistema AIP' }}</p>
            <span class="text-[10px] font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-widest mt-1 block">Gestión Técnica</span>
        </div>
    </div>

    <!-- NAVIGATION -->
    <nav class="flex-1 px-4 py-6 space-y-8 overflow-y-auto relative z-10 custom-scrollbar">

        @php
            $linkClass = "group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 text-sm font-medium relative overflow-hidden";
            $activeClass = "bg-gray-100 text-gray-900 dark:bg-white/10 dark:text-white shadow-[0_0_20px_rgba(255,255,255,0.02)]";
            $inactiveClass = "hover:bg-gray-50 hover:text-gray-900 dark:hover:bg-white/[0.03] dark:hover:text-white text-gray-600 dark:text-gray-400";
            $iconContainer = "w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-white/5 group-hover:bg-indigo-500/10 transition-colors duration-200";
            $iconActive = "text-indigo-600 dark:text-indigo-400";
            $iconInactive = "text-gray-400 dark:text-gray-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400";
        @endphp

        @if(!auth()->user()->hasRole('supervisor'))
        <!-- MAIN SECTION -->
        <div class="space-y-1">
            <h3 class="px-3 text-[10px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] mb-3">Principal</h3>
            
            <a href="{{ route('dashboard') }}"
               class="{{ $linkClass }} {{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }}">
                @if(request()->routeIs('dashboard'))
                    <div class="absolute left-0 top-1/4 bottom-1/4 w-1 bg-indigo-500 rounded-full"></div>
                @endif
                <div class="{{ $iconContainer }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? $iconActive : $iconInactive }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10" />
                    </svg>
                </div>
                Dashboard
            </a>
        </div>
        @endif

        @if(auth()->user()->hasRole('administrador'))
        <!-- INVENTARIO SECTION -->
        <div class="space-y-1">
            <h3 class="px-3 text-[10px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] mb-3">Inventario</h3>

            <a href="{{ route('equipos.index') }}"
               class="{{ $linkClass }} {{ request()->routeIs('equipos.*') ? $activeClass : $inactiveClass }}">
                @if(request()->routeIs('equipos.*'))
                    <div class="absolute left-0 top-1/4 bottom-1/4 w-1 bg-indigo-500 rounded-full"></div>
                @endif
                <div class="{{ $iconContainer }}">
                    <x-heroicon-o-computer-desktop class="w-4 h-4 {{ request()->routeIs('equipos.*') ? $iconActive : $iconInactive }}" />
                </div>
                Equipos
            </a>

            <a href="{{ route('componentes.index') }}"
               class="{{ $linkClass }} {{ request()->routeIs('componentes.*') ? $activeClass : $inactiveClass }}">
                @if(request()->routeIs('componentes.*'))
                    <div class="absolute left-0 top-1/4 bottom-1/4 w-1 bg-indigo-500 rounded-full"></div>
                @endif
                <div class="{{ $iconContainer }}">
                    <x-heroicon-o-cpu-chip class="w-4 h-4 {{ request()->routeIs('componentes.*') ? $iconActive : $iconInactive }}" />
                </div>
                Componentes
            </a>

            <!-- CATALOGS DROPDOWN -->
            <div x-data="{ open: {{ request()->routeIs('tipos-equipos.*') || request()->routeIs('categorias.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                    class="w-full {{ $linkClass }} {{ request()->routeIs('tipos-equipos.*') || request()->routeIs('categorias.*') ? $activeClass : $inactiveClass }}">
                    <div class="{{ $iconContainer }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('tipos-equipos.*') || request()->routeIs('categorias.*') ? $iconActive : $iconInactive }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </div>
                    <span class="flex-1 text-left">Catálogos</span>
                    <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="ml-11 space-y-1 border-l border-gray-100 dark:border-l-white/5 pl-2">
                    <a href="{{ route('tipos-equipos.index') }}"
                       class="block px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('tipos-equipos.*') ? 'text-indigo-600 font-bold dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                        Tipos de Equipos
                    </a>
                    <a href="{{ route('categorias.index') }}"
                       class="block px-3 py-2 text-xs rounded-lg transition-colors {{ request()->routeIs('categorias.*') ? 'text-indigo-600 font-bold dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">
                        Categorías
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('tecnico') || auth()->user()->hasRole('supervisor'))
        <!-- MAINTENANCE & REPORTS SECTION -->
        <div class="space-y-1">
            <h3 class="px-3 text-[10px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] mb-3">Gestión y Datos</h3>

            @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('tecnico'))
            <a href="{{ route('mantenimientos.index') }}"
               class="{{ $linkClass }} {{ request()->routeIs('mantenimientos.*') ? $activeClass : $inactiveClass }}">
                @if(request()->routeIs('mantenimientos.*'))
                    <div class="absolute left-0 top-1/4 bottom-1/4 w-1 bg-indigo-500 rounded-full"></div>
                @endif
                <div class="{{ $iconContainer }}">
                    <x-heroicon-o-wrench-screwdriver class="w-4 h-4 {{ request()->routeIs('mantenimientos.*') ? $iconActive : $iconInactive }}" />
                </div>
                {{ auth()->user()->hasRole('tecnico') ? 'Mis Mantenimientos' : 'Gestión de Mantenimientos' }}
            </a>
            @endif

            @if(auth()->user()->hasRole('administrador'))
            <a href="{{ route('carga-tecnico.index') }}"
               class="{{ $linkClass }} {{ request()->routeIs('carga-tecnico.*') ? $activeClass : $inactiveClass }}">
                @if(request()->routeIs('carga-tecnico.*'))
                    <div class="absolute left-0 top-1/4 bottom-1/4 w-1 bg-indigo-500 rounded-full"></div>
                @endif
                <div class="{{ $iconContainer }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('carga-tecnico.*') ? $iconActive : $iconInactive }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                Carga de Trabajo
            </a>
            @endif

            @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('supervisor') || auth()->user()->hasRole('tecnico'))
            <a href="{{ route('reportes.index') }}"
               class="{{ $linkClass }} {{ request()->routeIs('reportes.*') ? $activeClass : $inactiveClass }}">
                @if(request()->routeIs('reportes.*'))
                    <div class="absolute left-0 top-1/4 bottom-1/4 w-1 bg-indigo-500 rounded-full"></div>
                @endif
                <div class="{{ $iconContainer }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('reportes.*') ? $iconActive : $iconInactive }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                Reportes Generados
            </a>
            @endif
        </div>
        @endif

        @if(auth()->user()->hasRole('administrador'))
        <!-- SYSTEM SECTION -->
        <div class="space-y-1">
            <h3 class="px-3 text-[10px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-[0.2em] mb-3">Sistema</h3>

            <a href="{{ route('usuarios.index') }}"
               class="{{ $linkClass }} {{ request()->routeIs('usuarios.*') ? $activeClass : $inactiveClass }}">
                @if(request()->routeIs('usuarios.*'))
                    <div class="absolute left-0 top-1/4 bottom-1/4 w-1 bg-indigo-500 rounded-full"></div>
                @endif
                <div class="{{ $iconContainer }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('usuarios.*') ? $iconActive : $iconInactive }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                Usuarios
            </a>

            <a href="{{ route('configuracion.index') }}"
               class="{{ $linkClass }} {{ request()->routeIs('configuracion.*') ? $activeClass : $inactiveClass }}">
                @if(request()->routeIs('configuracion.*'))
                    <div class="absolute left-0 top-1/4 bottom-1/4 w-1 bg-indigo-500 rounded-full"></div>
                @endif
                <div class="{{ $iconContainer }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('configuracion.*') ? $iconActive : $iconInactive }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                Configuración
            </a>
        </div>
        @endif

     </nav>

    <!-- USER SECTION -->
    <div class="p-4 mt-auto relative z-10 border-t border-gray-100 dark:border-t-white/5 bg-gray-50 dark:bg-black/20 backdrop-blur-sm transition-colors duration-300">
        <div class="flex items-center gap-3 p-3 bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-200 dark:border-white/[0.05] shadow-sm dark:shadow-xl group transition-colors duration-300">
            <div class="relative">
                @if(Auth::user()->foto && file_exists(public_path(Auth::user()->foto)))
                    <img src="{{ asset(Auth::user()->foto) }}"
                         class="w-10 h-10 rounded-xl object-cover border border-gray-100 dark:border-white/10 group-hover:border-indigo-500/50 transition-colors duration-300" alt="Usuario">
                @else
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white text-sm font-bold border border-gray-100 dark:border-white/10 group-hover:border-indigo-500/50 transition-colors duration-300">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-[#09090b] rounded-full"></div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-gray-900 dark:text-white leading-none truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-gray-500 truncate mt-1">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="w-full py-2.5 rounded-xl bg-gray-100 hover:bg-rose-500/10 text-gray-600 hover:text-rose-500 dark:bg-white/5 dark:text-gray-400 dark:hover:text-rose-500 text-xs font-bold transition-all duration-200 border border-transparent hover:border-rose-500/20 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Cerrar Sesión
            </button>
        </form>
    </div>

</aside>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.05);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.15);
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.1);
    }
</style>
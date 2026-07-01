<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema') }}</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Fuente -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Transición suave para el cambio de modo día/noche */
        *, *::before, *::after {
            transition-property: background-color, border-color, color, fill, stroke;
            transition-duration: 300ms;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* 🔥 Adaptación Automática a Modo Oscuro para todo el proyecto 🔥 */
        .dark body, .dark main { background-color: #111827 !important; color: #f9fafb !important; }
        .dark .bg-white { background-color: #1f2937 !important; }
        .dark .bg-gray-50, .dark .bg-gray-50\/50 { background-color: #111827 !important; }
        .dark .bg-gray-100, .dark .bg-gray-100\/80 { background-color: #374151 !important; }
        .dark .bg-gray-200 { background-color: #4b5563 !important; }
        .dark .border-gray-100, .dark .border-gray-200, .dark .border-gray-300, .dark .border { border-color: #374151 !important; }
        .dark .text-gray-900, .dark .text-gray-800, .dark .text-gray-700 { color: #f3f4f6 !important; }
        .dark .text-gray-600, .dark .text-gray-500 { color: #9ca3af !important; }
        .dark th { background-color: #374151 !important; color: #d1d5db !important; }
        .dark tr:hover td { background-color: #374151 !important; }
        .dark .divide-gray-100 > :not([hidden]) ~ :not([hidden]), .dark .divide-y > :not([hidden]) ~ :not([hidden]) { border-color: #374151 !important; }
        .dark input, .dark select, .dark textarea { background-color: #374151 !important; color: #f3f4f6 !important; border-color: #4b5563 !important; }
        .dark input::placeholder, .dark textarea::placeholder { color: #9ca3af !important; }
        
        /* Asegurar que el sidebar no se vea afectado accidentalmente */
        .dark aside .bg-white { background-color: transparent !important; }
    </style>

    @stack('styles')

</head>

<body class="bg-gray-50 text-gray-800 transition-colors duration-300" 
      x-data="{ 
          sidebarOpen: true, 
          darkMode: localStorage.getItem('darkMode') === 'true' 
      }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val)); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); $watch('darkMode', val => { if(val) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); })"
      :class="{ 'dark': darkMode }">

<div class="flex min-h-screen">
    <!-- SIDEBAR -->
    <aside x-show="sidebarOpen" 
           x-transition:enter="transition ease-out duration-300" 
           x-transition:enter-start="-translate-x-full" 
           x-transition:enter-end="translate-x-0" 
           x-transition:leave="transition ease-in duration-300" 
           x-transition:leave-start="translate-x-0" 
           x-transition:leave-end="-translate-x-full" 
           class="flex-shrink-0 z-50 sticky top-0 h-screen">
        @include('layouts.navigation')
    </aside>

    <!-- CONTENIDO -->
    <div class="flex-1 flex flex-col min-w-0 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <!-- TOP BAR -->
        <header class="h-16 bg-white border-b flex items-center justify-between px-6 sticky top-0 z-40 shadow-sm transition-colors duration-300 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors dark:hover:bg-gray-700 dark:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex flex-col">
                    @if(isset($header))
                        {{ $header }}
                    @else
                        @yield('header')
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- TEMA OSCURO / CLARO -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-full hover:bg-gray-100 text-gray-500 transition-colors dark:hover:bg-gray-700 dark:text-yellow-400">
                    <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
                
                <!-- NOTIFICACIONES -->
                <div x-data="{ 
                    open: false, 
                    count: 0, 
                    recientes: [],
                    playNotificationSound() {
                        const play = () => {
                            try {
                                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                                
                                const playTone = (freq, duration, delay) => {
                                    const osc = audioCtx.createOscillator();
                                    const gainNode = audioCtx.createGain();
                                    
                                    osc.connect(gainNode);
                                    gainNode.connect(audioCtx.destination);
                                    
                                    osc.type = 'sine';
                                    osc.frequency.setValueAtTime(freq, audioCtx.currentTime + delay);
                                    
                                    gainNode.gain.setValueAtTime(0.06, audioCtx.currentTime + delay);
                                    gainNode.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + delay + duration);
                                    
                                    osc.start(audioCtx.currentTime + delay);
                                    osc.stop(audioCtx.currentTime + delay + duration);
                                };
                                
                                playTone(523.25, 0.35, 0);
                                playTone(659.25, 0.50, 0.12);
                            } catch (e) {
                                console.error('Error al reproducir audio:', e);
                            }
                        };

                        try {
                            const audioCtxTemp = new (window.AudioContext || window.webkitAudioContext)();
                            if (audioCtxTemp.state === 'suspended') {
                                // Si el navegador tiene el audio suspendido, esperamos al primer clic para sonar
                                const startAudio = () => {
                                    play();
                                    document.removeEventListener('click', startAudio);
                                    document.removeEventListener('keydown', startAudio);
                                };
                                document.addEventListener('click', startAudio);
                                document.addEventListener('keydown', startAudio);
                            } else {
                                play();
                            }
                        } catch (e) {
                            play();
                        }
                    },
                    async getNotificaciones() {
                        const res = await fetch('{{ route('notificaciones.recientes') }}');
                        const recientes = await res.json();
                        this.recientes = recientes;
                        
                        const resCount = await fetch('{{ route('notificaciones.conteo') }}');
                        const dataCount = await resCount.json();
                        this.count = dataCount.count;
                        
                        if (recientes.length > 0) {
                            const latestNotification = recientes[0];
                            const lastHeardId = localStorage.getItem('lastHeardNotificationId');
                            
                            if (lastHeardId === null) {
                                // Registro inicial en frío del navegador
                                localStorage.setItem('lastHeardNotificationId', latestNotification.id);
                            } else if (parseInt(latestNotification.id) > parseInt(lastHeardId)) {
                                // Si hay una notificación con ID superior a la última registrada en este navegador, suena
                                this.playNotificationSound();
                                localStorage.setItem('lastHeardNotificationId', latestNotification.id);
                            }
                        }
                    }
                }" x-init="getNotificaciones(); setInterval(() => getNotificaciones(), 20000)" class="relative">
                    
                    <button @click="open = !open" class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <template x-if="count > 0">
                            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-600 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white" x-text="count"></span>
                        </template>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition 
                        class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden dark:bg-gray-800 dark:border-gray-700">
                        <div class="px-5 py-4 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between gap-4">
                            <h3 class="text-sm font-bold text-gray-800 dark:text-white">Notificaciones</h3>
                            <a href="{{ route('notificaciones.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Ver todas</a>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="recientes.length === 0">
                                <div class="px-5 py-8 text-center">
                                    <p class="text-sm text-gray-400">No hay notificaciones nuevas</p>
                                </div>
                            </template>
                            <template x-for="(n, index) in recientes" :key="n.id">
                                <div class="relative group block hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-50 dark:border-gray-700 last:border-0 transition-colors"
                                     :class="[
                                         n.tipo === 'finalizado' ? 'bg-emerald-50 dark:bg-emerald-900/20' : (n.titulo.includes('VENCIDO') || n.tipo === 'falla_critica' ? 'bg-red-50 dark:bg-red-900/20' : ''),
                                         !n.leida ? 'border-l-4 border-l-indigo-500' : ''
                                     ]">
                                    <template x-if="n.enlace">
                                        <a :href="n.enlace" class="block px-5 py-4 pr-10">
                                            <div class="flex justify-between items-start mb-1">
                                                <div class="flex items-center gap-2">
                                                    <template x-if="!n.leida">
                                                        <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                                                    </template>
                                                    <p class="text-xs font-black text-gray-800 dark:text-gray-200" x-text="n.titulo"></p>
                                                </div>
                                                <template x-if="n.titulo.includes('VENCIDO')">
                                                    <span class="text-[9px] bg-red-600 text-white px-1.5 py-0.5 rounded font-black uppercase">Urgente</span>
                                                </template>
                                            </div>
                                            <p class="text-xs text-gray-500 line-clamp-2" x-text="n.mensaje"></p>
                                            <p class="text-[10px] text-gray-400 mt-2 font-medium" x-text="n.hace"></p>
                                        </a>
                                    </template>
                                    <template x-if="!n.enlace">
                                        <div class="block px-5 py-4 pr-10">
                                            <div class="flex justify-between items-start mb-1">
                                                <div class="flex items-center gap-2">
                                                    <template x-if="!n.leida">
                                                        <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                                                    </template>
                                                    <p class="text-xs font-black text-gray-800 dark:text-gray-200" x-text="n.titulo"></p>
                                                </div>
                                                <template x-if="n.titulo.includes('VENCIDO')">
                                                    <span class="text-[9px] bg-red-600 text-white px-1.5 py-0.5 rounded font-black uppercase">Urgente</span>
                                                </template>
                                            </div>
                                            <p class="text-xs text-gray-500 line-clamp-2" x-text="n.mensaje"></p>
                                            <p class="text-[10px] text-gray-400 mt-2 font-medium" x-text="n.hace"></p>
                                        </div>
                                    </template>
                                    <button @click.stop="
                                        fetch(`/notificaciones/${n.id}/leida`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content'),
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json'
                                            }
                                        });
                                        recientes.splice(index, 1);
                                        count = Math.max(0, count - 1);
                                    " class="absolute top-4 right-4 p-1 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100/80 dark:hover:bg-gray-600/80 transition-all opacity-0 group-hover:opacity-100 focus:opacity-100" title="Marcar como leída">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6 lg:p-8 flex-1">
            @hasSection('content')
                @yield('content')
            @else
                @isset($slot)
                    {{ $slot }}
                @endisset
            @endif
        </main>
    </div>
</div>

    @stack('scripts')
</body>
</html>

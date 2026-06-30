@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition.duration.300ms
        id="alert_error" class="flex items-center justify-between bg-red-100 border-l-4 border-red-500 text-red-750 p-4 mb-6 rounded-lg shadow-sm dark:bg-rose-950/30 dark:border-rose-500 dark:text-rose-450"
        role="alert">
        <p>{{ session('error') }}</p>
        <button @click="show = false" class="text-red-500 hover:text-red-800 dark:text-rose-400 dark:hover:text-rose-250 p-1 rounded-lg hover:bg-red-200/50 dark:hover:bg-rose-900/50 transition-colors" title="Cerrar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif


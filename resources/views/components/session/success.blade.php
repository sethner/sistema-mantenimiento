@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition.duration.300ms
        id="alert_success" class="flex items-center justify-between bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm dark:bg-emerald-950/30 dark:border-emerald-500 dark:text-emerald-400"
        role="alert">
        <p>{{ session('success') }}</p>
        <button @click="show = false" class="text-green-500 hover:text-green-800 dark:text-emerald-400 dark:hover:text-emerald-250 p-1 rounded-lg hover:bg-green-200/50 dark:hover:bg-emerald-900/50 transition-colors" title="Cerrar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif



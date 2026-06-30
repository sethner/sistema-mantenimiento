@extends('layouts.app')

@section('header')
    <div class="flex items-baseline gap-4">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Notificaciones</h2>
        @if($notificaciones->where('leida', false)->count() > 0)
            <span class="text-gray-300">|</span>
            <form action="{{ route('notificaciones.marcar-todas') }}" method="POST">
                @csrf
                <button class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition hover:underline">
                    Marcar todas como leídas
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    @forelse($notificaciones as $n)
        <div class="bg-white rounded-2xl p-5 border shadow-sm flex items-start gap-4 transition-all {{ str_contains($n->titulo, 'VENCIDO') || $n->tipo === 'falla_critica' ? 'border-red-100 bg-red-50/30' : ($n->tipo === 'finalizado' ? 'border-emerald-100 bg-emerald-50/30' : 'border-gray-100') }} {{ !$n->leida ? 'border-l-4 border-l-indigo-500' : '' }}">
            <div class="p-2.5 rounded-xl {{ $n->leida ? 'bg-gray-50 text-gray-400' : (str_contains($n->titulo, 'VENCIDO') || $n->tipo === 'falla_critica' ? 'bg-red-100 text-red-500' : ($n->tipo === 'finalizado' ? 'bg-emerald-100 text-emerald-500' : 'bg-indigo-50 text-indigo-500')) }}">
                @switch($n->tipo)
                    @case('asignacion')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        @break
                    @case('alerta_vencimiento')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @break
                    @case('falla_critica')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        @break
                    @case('finalizado')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @break
                    @default
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                @endswitch
            </div>
            
            <div class="flex-1">
                <div class="flex items-center justify-between gap-4 mb-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-gray-900">{{ $n->titulo }}</h3>
                        @if(str_contains($n->titulo, 'VENCIDO'))
                            <span class="text-[9px] bg-red-600 text-white px-1.5 py-0.5 rounded font-black uppercase">Urgente</span>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest shrink-0">{{ $n->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-3">{{ $n->mensaje }}</p>
                
                <div class="flex items-center gap-4">
                    @if($n->enlace)
                        <a href="{{ $n->enlace }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ver detalle</a>
                    @endif
                    @if(!$n->leida)
                        <button onclick="marcarLeida({{ $n->id }}, this)" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition">Marcar como leída</button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl p-10 border border-gray-100 shadow-sm text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            </div>
            <h3 class="text-gray-800 font-bold mb-1">Sin notificaciones</h3>
            <p class="text-sm text-gray-400">Te avisaremos cuando haya algo importante para ti.</p>
        </div>
    @endforelse

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
        <div>
            {{ $notificaciones->links() }}
        </div>
        
        @if($notificaciones->count() > 0)
            <form action="{{ route('notificaciones.limpiar') }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar todo el historial de notificaciones?')">
                @csrf
                <button class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-50 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Eliminar historial completo
                </button>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function marcarLeida(id, btn) {
        try {
            const res = await fetch(`{{ url('/notificaciones') }}/${id}/leida`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                const card = btn.closest('.bg-white');
                card.classList.remove('border-l-4', 'border-l-indigo-500');
                btn.remove();
            }
        } catch (e) {
            console.error(e);
        }
    }
</script>
@endpush

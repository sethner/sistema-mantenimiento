@component('mail::message')
# ¡Hola {{ $notifiable->name }}!

Se ha finalizado y registrado con éxito un mantenimiento en el sistema.

@component('mail::panel')
### Detalles del Servicio Completado

* **Equipo:** {{ $mantenimiento->equipo->nombre }} (Código: {{ $mantenimiento->equipo->codigo }})
* **Tipo de Mantenimiento:** {{ ucfirst($mantenimiento->tipo) }}
* **Técnico Encargado:** {{ $mantenimiento->usuario->name }}
* **Fecha de Finalización:** {{ $mantenimiento->fecha ? $mantenimiento->fecha->format('d/m/Y') : now()->format('d/m/Y') }}
* **Costo Total:** @if($mantenimiento->costo) S/. {{ number_format($mantenimiento->costo, 2) }} @else Sin costo registrado @endif

#### Diagnóstico Técnico:
{{ $mantenimiento->diagnostico ?: 'No especificado.' }}

#### Acción Realizada:
{{ $mantenimiento->accion ?: 'No especificada.' }}
@endcomponent

@component('mail::button', ['url' => route('mantenimientos.show', $mantenimiento->id), 'color' => 'success'])
Ver Mantenimiento
@endcomponent

El estado del equipo ha sido actualizado a **Operativo**.
@endcomponent

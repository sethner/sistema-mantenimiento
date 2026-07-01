@component('mail::message')
# ¡Atención {{ $notifiable->name }}!

Se ha detectado un **mantenimiento VENCIDO** que aún no ha sido finalizado.

@component('mail::panel')
### Detalles del Mantenimiento Vencido

* **Equipo:** {{ $mantenimiento->equipo->nombre }} (Código: {{ $mantenimiento->equipo->codigo }})
* **Tipo de Servicio:** {{ ucfirst($mantenimiento->tipo) }}
* **Fecha Programada:** {{ $mantenimiento->fecha->format('d/m/Y') }} (¡Vencido!)
* **Técnico Asignado:** {{ $mantenimiento->usuario ? $mantenimiento->usuario->name : 'No asignado' }}

#### Descripción del requerimiento:
{{ $mantenimiento->descripcion }}
@endcomponent

@component('mail::button', ['url' => route('mantenimientos.show', $mantenimiento->id), 'color' => 'error'])
Ver Orden de Mantenimiento
@endcomponent

*Por favor, ingresa al sistema para gestionar o finalizar esta orden de trabajo a la brevedad posible.*
@endcomponent

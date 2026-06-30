@component('mail::message')
# Hola Administrador {{ $notifiable->name }},

Se ha registrado y programado un nuevo servicio de mantenimiento en la plataforma.

@component('mail::panel')
### Ficha del Servicio Registrado

* **Equipo:** {{ $mantenimiento->equipo->nombre }} (Código: {{ $mantenimiento->equipo->codigo }})
* **Técnico Asignado:** {{ $mantenimiento->usuario->name }}
* **Tipo de Mantenimiento:** {{ ucfirst($mantenimiento->tipo) }}
* **Fecha Programada:** {{ $mantenimiento->fecha ? $mantenimiento->fecha->format('d/m/Y') : 'Inmediata' }}
@endcomponent

@component('mail::button', ['url' => route('mantenimientos.show', $mantenimiento->id), 'color' => 'primary'])
Ver Orden de Trabajo
@endcomponent

*Este es un aviso automático generado por el Sistema de Control de Mantenimiento.*
@endcomponent


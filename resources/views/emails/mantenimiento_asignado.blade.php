@component('mail::message')
# ¡Hola {{ $notifiable->name }}!

Se te ha asignado una nueva **orden de trabajo** de mantenimiento.

@component('mail::panel')
### Detalles del Requerimiento Asignado

* **Equipo:** {{ $mantenimiento->equipo->nombre }} (Código: {{ $mantenimiento->equipo->codigo }})
* **Tipo de Servicio:** {{ ucfirst($mantenimiento->tipo) }}
* **Fecha Programada:** {{ $mantenimiento->fecha ? $mantenimiento->fecha->format('d/m/Y') : 'Programación inmediata' }}

#### Descripción del requerimiento:
{{ $mantenimiento->descripcion }}
@endcomponent

@component('mail::button', ['url' => route('mantenimientos.show', $mantenimiento->id), 'color' => 'primary'])
Iniciar Atención Técnica
@endcomponent

*Por favor, ingresa al sistema para registrar el diagnóstico y las acciones correspondientes una vez atendido el equipo.*
@endcomponent


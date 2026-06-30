@component('mail::message')
# Hola {{ $notifiable->name }},

@if ($esVencido)
Te informamos que el mantenimiento preventivo programado para este equipo ya ha **VENCIDO**. Es de carácter urgente programar su revisión.
@else
Te recordamos que un equipo del sistema está próximo a requerir su mantenimiento preventivo programado.
@endif

@component('mail::panel')
### Detalles del Recordatorio Preventivo

* **Equipo:** {{ $equipo->nombre }} (Código: {{ $equipo->codigo }})
* **Fecha Límite Programada:** {{ $equipo->proximo_mantenimiento ? $equipo->proximo_mantenimiento->format('d/m/Y') : 'No especificada' }}
* **Estado del Plazo:** <span style="color: {{ $esVencido ? '#ef4444' : '#f59e0b' }}; font-weight: bold;">{{ $esVencido ? 'VENCIDO' : 'Próximo a Vencer' }}</span>
@endcomponent

@component('mail::button', ['url' => route('equipos.show', $equipo->id), 'color' => $esVencido ? 'error' : 'primary'])
Ver Ficha del Equipo
@endcomponent

*Por favor, coordina y programa el mantenimiento preventivo correspondiente para asegurar la continuidad operativa y rendimiento del equipo.*
@endcomponent


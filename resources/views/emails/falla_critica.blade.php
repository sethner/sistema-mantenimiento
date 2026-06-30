@component('mail::message')
# ¡Atención Administrador {{ $notifiable->name }}!

Se ha detectado y registrado una **falla crítica** en uno de los equipos principales.

@component('mail::panel')
### Detalles del Equipo Afectado

* **Equipo:** {{ $equipo->nombre }}
* **Código de Inventario:** {{ $equipo->codigo }}
* **Estado Actual:** <span style="color: #ef4444; font-weight: bold;">{{ ucwords(str_replace('_', ' ', $equipo->estado)) }}</span>
@endcomponent

@component('mail::button', ['url' => route('equipos.show', $equipo->id), 'color' => 'error'])
Ver Detalles del Equipo
@endcomponent

*Se recomienda revisar la ficha técnica del equipo y coordinar un mantenimiento correctivo a la brevedad para mitigar riesgos.*
@endcomponent
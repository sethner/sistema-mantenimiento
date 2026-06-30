<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Mantenimiento;

/**
 * Clase User
 * Modela un usuario del sistema (técnicos, administradores, directores).
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $foto
 * @property \Carbon\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Atributos asignables de forma masiva
    protected $fillable = [
        'name',
        'email',
        'password',
        'foto'
    ];

    // Atributos ocultos en serializaciones
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Conversiones de atributos automáticas
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación muchos a muchos con Role.
     * Vincula los roles de acceso que posee el usuario.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Helper de seguridad para verificar si un usuario tiene un rol determinado.
     * Realiza una comparación no sensible a mayúsculas y minúsculas.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()
            ->whereRaw('LOWER(nombre) = ?', [strtolower($role)])
            ->exists();
    }

    /**
     * Relación uno a muchos con el modelo Mantenimiento.
     * Retorna todas las órdenes de mantenimiento asignadas o resueltas por el usuario.
     */
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class);
    }

    /**
     * Relación uno a muchos con el modelo Notificacion.
     * Obtiene el listado completo de alertas enviadas al usuario.
     */
    public function notificaciones()
    {
        return $this->hasMany(\App\Models\Notificacion::class);
    }

    /**
     * Obtiene únicamente las notificaciones no leídas del usuario.
     */
    public function notificacionesNoLeidas()
    {
        return $this->notificaciones()->where('leida', false);
    }

    /**
     * Sobrescribe el método nativo de Laravel para redirigir las notificaciones
     * a nuestra relación personalizada 'notificaciones()' en español.
     */
    public function notifications()
    {
        return $this->notificaciones();
    }

    /**
     * Sobrescribe el método nativo de Laravel para redirigir las notificaciones no leídas.
     */
    public function unreadNotifications()
    {
        return $this->notificacionesNoLeidas();
    }

    /**
     * Devuelve el canal de base de datos para notificaciones de Laravel,
     * adaptando el comportamiento nativo a nuestra tabla en español.
     */
    public function routeNotificationForDatabase($notification)
    {
        return new class($this) {
            protected $user;

            public function __construct($user)
            {
                $this->user = $user;
            }

            public function create(array $attributes)
            {
                $data = $attributes['data'] ?? [];

                return $this->user->notificaciones()->create([
                    'tipo'    => $data['tipo'] ?? null,
                    'titulo'  => $data['titulo'] ?? null,
                    'mensaje' => $data['mensaje'] ?? null,
                    'enlace'  => $data['enlace'] ?? null,
                    'leida'   => isset($attributes['read_at']) ? !is_null($attributes['read_at']) : ($data['leida'] ?? false),
                ]);
            }
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase Notificacion
 * Modelo personalizado para almacenar las alertas y notificaciones del sistema de mantenimiento.
 *
 * @property int $id
 * @property int $user_id
 * @property string $tipo ('asignacion', 'alerta_vencimiento', 'falla_critica')
 * @property string $titulo
 * @property string $mensaje
 * @property string|null $enlace
 * @property bool $leida
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Notificacion extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'notificaciones';

    // Atributos asignables de forma masiva
    protected $fillable = [
        'user_id', 'tipo', 'titulo', 'mensaje', 'enlace', 'leida',
    ];

    // Conversión automática de tipos
    protected $casts = [
        'leida' => 'boolean',
    ];

    /**
     * Relación de pertenencia (muchos a uno) con User.
     * Obtiene el usuario destinatario de la notificación.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Crea una notificación para informar a un técnico que se le asignó un mantenimiento.
     */
    public static function asignacion(int $userId, string $equipoNombre, int $mantenimientoId): void
    {
        static::create([
            'user_id' => $userId,
            'tipo'    => 'asignacion',
            'titulo'  => 'Nuevo mantenimiento asignado',
            'mensaje' => "Se te asignó un mantenimiento en el equipo: {$equipoNombre}.",
            'enlace'  => route('mantenimientos.show', $mantenimientoId),
        ]);
    }

    /**
     * Crea una notificación de alerta para mantenimientos programados que están próximos a vencer.
     */
    public static function alertaVencimiento(int $userId, string $equipoNombre, int $mantenimientoId, string $fecha): void
    {
        static::create([
            'user_id' => $userId,
            'tipo'    => 'alerta_vencimiento',
            'titulo'  => 'Mantenimiento próximo a vencer',
            'mensaje' => "El mantenimiento de {$equipoNombre} vence el {$fecha}.",
            'enlace'  => route('mantenimientos.show', $mantenimientoId),
        ]);
    }

    /**
     * Crea una notificación para informar a los administradores de una falla crítica.
     */
    public static function fallaCritica(int $adminId, string $equipoNombre, int $equipoId): void
    {
        static::create([
            'user_id' => $adminId,
            'tipo'    => 'falla_critica',
            'titulo'  => 'Equipo con falla crítica',
            'mensaje' => "El equipo {$equipoNombre} ha registrado una nueva falla.",
            'enlace'  => route('equipos.show', $equipoId),
        ]);
    }

    /**
     * Determina si la notificación ha sido leída.
     */
    public function read(): bool
    {
        return $this->leida;
    }

    /**
     * Determina si la notificación no ha sido leída.
     */
    public function unread(): bool
    {
        return !$this->leida;
    }

    /**
     * Marca la notificación como leída actualizando la base de datos.
     */
    public function markAsRead(): void
    {
        if (!$this->leida) {
            $this->forceFill(['leida' => true])->save();
        }
    }

    /**
     * Marca la notificación como no leída.
     */
    public function markAsUnread(): void
    {
        if ($this->leida) {
            $this->forceFill(['leida' => false])->save();
        }
    }

    /**
     * Accesor para simular la columna nativa de notificaciones de Laravel 'read_at'.
     */
    public function getReadAtAttribute()
    {
        return $this->leida ? $this->updated_at : null;
    }

    /**
     * Mutador para mapear 'read_at' y guardarlo como booleano en la columna 'leida'.
     */
    public function setReadAtAttribute($value)
    {
        $this->attributes['leida'] = !is_null($value);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase Role
 * Representa los roles de acceso en el sistema (ej. Administrador, Técnico, etc.).
 *
 * @property int $id
 * @property string $nombre
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Role extends Model
{
    // Atributos asignables de forma masiva
    protected $fillable = ['nombre'];

    /**
     * Relación muchos a muchos con User.
     * Obtiene los usuarios que tienen asignado este rol.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
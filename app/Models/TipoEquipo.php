<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase TipoEquipo
 * Define el tipo o categoría general de los equipos (ej. Computadora, Laptop, etc.).
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $imagen
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class TipoEquipo extends Model
{
    // Define el nombre de la tabla en la BD
    protected $table = 'tipo_equipos';

    // Atributos asignables de forma masiva
    protected $fillable = ['nombre', 'imagen'];

    /**
     * Relación uno a muchos con el modelo Componente.
     * Obtiene todos los componentes compatibles creados bajo este tipo de equipo.
     */
    public function componentes()
    {
        return $this->hasMany(Componente::class, 'tipo_id', 'id');
    }
}

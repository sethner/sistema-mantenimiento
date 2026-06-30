<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase CategoriaComponente
 * Representa la categoría a la que pertenece un componente del sistema de mantenimiento.
 *
 * @property int $id
 * @property string $nombre
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class CategoriaComponente extends Model
{
    // Define el nombre de la tabla asociada en la base de datos
    protected $table = 'categorias_componentes';

    // Atributos que se pueden asignar de manera masiva
    protected $fillable = [
        'nombre'
    ];

    /**
     * Relación uno a muchos con el modelo Componente.
     * Obtiene todos los componentes que pertenecen a esta categoría.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function componentes()
    {
        return $this->hasMany(Componente::class, 'categoria_id');
    }
}
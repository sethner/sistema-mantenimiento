<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TipoEquipo;

/**
 * Clase Componente
 * Representa los componentes y repuestos vinculables a los equipos del sistema.
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $imagen
 * @property int $tipo_id
 * @property int|null $categoria_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Componente extends Model
{
    // Atributos asignables de forma masiva
    protected $fillable = [
        'nombre',
        'imagen',
        'tipo_id',
        'categoria_id',
    ];

    /**
     * Relación de pertenencia (muchos a uno) con TipoEquipo.
     * Define el tipo de equipo compatible con este componente.
     */
    public function tipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'tipo_id', 'id');
    }

    /**
     * Relación muchos a muchos con Equipo a través de la tabla intermedia 'equipo_componentes'.
     * Obtiene los equipos que tienen instalado o vinculado este componente.
     */
    public function equipos()
{
    return $this->belongsToMany(
        \App\Models\Equipo::class,
        'equipo_componentes'
    );
}

    /**
     * Relación de pertenencia (muchos a uno) con CategoriaComponente.
     * Obtiene la categoría a la que pertenece este componente (ej: Memoria, Almacenamiento, etc.).
     */
    public function categoria()
{
    return $this->belongsTo(CategoriaComponente::class, 'categoria_id');
}
    
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase MantenimientoFoto
 * Modela las evidencias fotográficas que los técnicos suben como justificación de un mantenimiento.
 *
 * @property int $id
 * @property int $mantenimiento_id
 * @property string $ruta
 * @property string $nombre_original
 * @property string|null $descripcion
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class MantenimientoFoto extends Model
{
    // Nombre de la tabla en la BD
    protected $table = 'mantenimiento_fotos';

    // Atributos asignables de forma masiva
    protected $fillable = [
        'mantenimiento_id', 'ruta', 'nombre_original', 'descripcion',
    ];

    /**
     * Relación de pertenencia (muchos a uno) con Mantenimiento.
     * Vincula la foto con la orden de mantenimiento correspondiente.
     */
    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class);
    }
}

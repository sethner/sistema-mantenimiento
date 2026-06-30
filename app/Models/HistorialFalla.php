<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase HistorialFalla
 * Registra y almacena el histórico de fallas críticas detectadas en los equipos o componentes individuales.
 *
 * @property int $id
 * @property int $equipo_id
 * @property int|null $mantenimiento_id
 * @property int|null $componente_id
 * @property string $descripcion
 * @property string $tipo
 * @property string|null $resolucion
 * @property \Carbon\Carbon $fecha
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class HistorialFalla extends Model
{
    // Define el nombre de la tabla en la base de datos
    protected $table = 'historial_fallas';

    // Atributos asignables de forma masiva
    protected $fillable = [
        'equipo_id', 'mantenimiento_id', 'componente_id',
        'descripcion', 'tipo', 'resolucion', 'fecha',
    ];

    // Conversión de tipos
    protected $casts = [
        'fecha' => 'date',
    ];

    /**
     * Relación de pertenencia con Equipo.
     * Obtiene el equipo en el que ocurrió la falla.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Relación de pertenencia con Mantenimiento.
     * Obtiene el mantenimiento bajo el cual se detectó o resolvió esta falla.
     */
    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class);
    }

    /**
     * Relación de pertenencia con Componente.
     * Obtiene el componente específico que falló (si aplica).
     */
    public function componente()
    {
        return $this->belongsTo(Componente::class);
    }
}

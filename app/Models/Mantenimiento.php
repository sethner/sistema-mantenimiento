<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MantenimientoFoto;
use App\Models\HistorialFalla;

/**
 * Clase Mantenimiento
 * Representa una orden de servicio técnico o mantenimiento preventivo/correctivo ejecutada sobre un equipo.
 *
 * @property int $id
 * @property int $equipo_id
 * @property int $user_id
 * @property string $tipo ('preventivo', 'correctivo')
 * @property string $descripcion
 * @property string|null $diagnostico
 * @property string|null $accion
 * @property \Carbon\Carbon $fecha
 * @property \Carbon\Carbon|null $proxima_fecha
 * @property string $estado ('pendiente', 'en_proceso', 'finalizado')
 * @property float|null $costo
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Mantenimiento extends Model
{
    // Atributos asignables de forma masiva
    protected $fillable = [
        'equipo_id',
        'user_id',
        'tipo',
        'descripcion',
        'diagnostico',
        'accion',
        'fecha',
        'proxima_fecha',
        'estado',
        'costo'
    ];

    /**
     * Relación uno a muchos con MantenimientoFoto.
     * Obtiene todas las evidencias fotográficas adjuntas a esta orden de mantenimiento.
     */
    public function fotos()
    {
        return $this->hasMany(MantenimientoFoto::class);
    }

    // Conversión de tipos
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'proxima_fecha' => 'date',
        ];
    }

    /**
     * Relación de pertenencia con Equipo.
     * Obtiene el equipo sobre el cual se realiza el mantenimiento.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Relación de pertenencia con User (Técnico).
     * Obtiene el técnico que tiene asignada la orden de mantenimiento.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

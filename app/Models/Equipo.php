<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TipoEquipo;

/**
 * Clase Equipo
 * Representa un equipo o activo tecnológico del sistema sujeto a control y mantenimiento.
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property int $tipo_id
 * @property string|null $marca
 * @property string|null $modelo
 * @property string $estado ('operativo', 'en_mantenimiento', 'con_falla', 'dado_de_baja')
 * @property int|null $frecuencia_mantenimiento (en meses)
 * @property \Carbon\Carbon|null $proximo_mantenimiento
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Equipo extends Model
{
    // Atributos asignables de forma masiva
    protected $fillable = [
        'codigo',
        'nombre',
        'tipo_id',
        'marca',
        'modelo',
        'estado',
        'frecuencia_mantenimiento',
        'proximo_mantenimiento'
    ];

    // Conversión automática de tipos (casting)
    protected $casts = [
        'proximo_mantenimiento' => 'date',
    ];

    /**
     * Relación uno a muchos con HistorialFalla.
     * Obtiene el listado de fallas registradas, ordenadas por fecha más reciente.
     */
    public function historialFallas()
    {
        return $this->hasMany(\App\Models\HistorialFalla::class)->latest('fecha');
    }

    /**
     * Relación muchos a muchos con el modelo Componente.
     * Vincula los componentes físicos que componen este equipo, almacenando el estado individual en la tabla pivote.
     */
    public function componentes()
    {
        return $this->belongsToMany(
            \App\Models\Componente::class,
            'equipo_componentes'
        )->withPivot('estado')->withTimestamps();
    }

    /**
     * Relación uno a muchos con el modelo Mantenimiento.
     * Obtiene el histórico completo de servicios de mantenimiento de este equipo.
     */
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class);
    }

    /**
     * Obtiene el último mantenimiento registrado usando la funcionalidad latestOfMany de Eloquent.
     */
    public function ultimoMantenimiento()
    {
        return $this->hasOne(Mantenimiento::class)->latestOfMany('fecha');
    }

    /**
     * Relación de pertenencia (muchos a uno) con TipoEquipo.
     * Obtiene el tipo/categoría de equipo (ej. Servidor, Laptop, Impresora).
     */
    public function tipo()
    {
        return $this->belongsTo(TipoEquipo::class);
    }
}

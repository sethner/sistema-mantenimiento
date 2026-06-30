<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clase Configuracion
 * Representa los ajustes generales de la institución del sistema (datos de cabecera, logo, ruc, etc.).
 *
 * @property int $id
 * @property string $nombre_institucion
 * @property string|null $logo_path
 * @property string|null $director_nombre
 * @property string|null $ruc
 * @property string|null $direccion
 * @property string|null $telefono
 * @property string|null $email
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string|null $logo_base64
 */
class Configuracion extends Model
{
    // Define el nombre de la tabla en la BD
    protected $table = 'configuraciones';

    // Atributos asignables de forma masiva
    protected $fillable = [
        'nombre_institucion',
        'logo_path',
        'director_nombre',
        'ruc',
        'direccion',
        'telefono',
        'email',
    ];

    /**
     * Accesor para codificar el logo en Base64.
     * Útil para incrustar el logo directamente en PDF o reportes impresos.
     *
     * @return string|null
     */
    public function getLogoBase64Attribute()
    {
        // Validar si la ruta del logo existe en el almacenamiento público
        if ($this->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo_path)) {
            try {
                $path = \Illuminate\Support\Facades\Storage::disk('public')->path($this->logo_path);
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                // Retorna la URI en formato data:image/...;base64,...
                return 'data:image/' . ($type === 'jpg' ? 'jpeg' : $type) . ';base64,' . base64_encode($data);
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
}

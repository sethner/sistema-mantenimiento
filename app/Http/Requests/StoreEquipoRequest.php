<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Clase StoreEquipoRequest
 * Valida la entrada de datos enviada al crear un nuevo equipo.
 */
class StoreEquipoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación aplicables para la creación de un equipo.
     */
    public function rules(): array
    {
        return [
            // Código único del equipo, máximo 100 caracteres
            'codigo' => ['required', 'string', 'max:100', 'unique:equipos,codigo'],
            'nombre' => ['required', 'string', 'max:100'],
            // El tipo de equipo debe existir en la tabla 'tipo_equipos'
            'tipo_id' => ['required', 'exists:tipo_equipos,id'],
            'marca' => ['nullable', 'string', 'max:100'],
            'modelo' => ['nullable', 'string', 'max:100'],
            // El estado del equipo debe ser uno de los permitidos
            'estado' => ['nullable', Rule::in(['operativo', 'en_mantenimiento', 'con_falla', 'dado_de_baja'])],
            // Frecuencia en meses, valor entero entre 1 y 24 meses
            'frecuencia_mantenimiento' => ['required', 'integer', 'min:1', 'max:24'],
        ];
    }

    /**
     * Mensajes personalizados para los errores de validación.
     */
    public function messages(): array
    {
        return [
            'codigo.required' => 'El campo código es obligatorio.',
            'codigo.string' => 'El campo código debe ser una cadena de texto.',
            'codigo.max' => 'El campo código no debe exceder los 100 caracteres.',
            'codigo.unique' => 'El código ya está en uso.',
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.string' => 'El campo nombre debe ser una cadena de texto.',
            'nombre.max' => 'El campo nombre no debe exceder los 100 caracteres.',
            'tipo_id.required' => 'El tipo de equipo es obligatorio.',
            'tipo_id.exists' => 'El tipo de equipo seleccionado no es válido.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'frecuencia_mantenimiento.required' => 'La frecuencia es obligatoria.',
            'frecuencia_mantenimiento.integer' => 'La frecuencia debe ser un número entero.',
            'frecuencia_mantenimiento.min' => 'La frecuencia mínima es 1 mes.',
            'frecuencia_mantenimiento.max' => 'La frecuencia máxima es 24 meses.',
        ];
    }
}

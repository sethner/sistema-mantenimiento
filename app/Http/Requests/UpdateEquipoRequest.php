<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $equipoId = $this->route('equipo')->id;

        return [
            'codigo' => ['required', 'string', 'max:100', Rule::unique('equipos', 'codigo')->ignore($equipoId)],
            'nombre' => ['required', 'string', 'max:100'],
            'tipo_id' => ['required', 'exists:tipo_equipos,id'],
            'marca' => ['nullable', 'string', 'max:100'],
            'modelo' => ['nullable', 'string', 'max:100'],
            'estado' => ['required', Rule::in(['operativo', 'en_mantenimiento', 'con_falla', 'dado_de_baja'])],
            'frecuencia_mantenimiento' => ['required', 'integer', 'min:1', 'max:24'],
        ];
    }

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
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'frecuencia_mantenimiento.required' => 'La frecuencia es obligatoria.',
            'frecuencia_mantenimiento.integer' => 'La frecuencia debe ser un número entero.',
            'frecuencia_mantenimiento.min' => 'La frecuencia mínima es 1 mes.',
            'frecuencia_mantenimiento.max' => 'La frecuencia máxima es 24 meses.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMantenimientoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        return [
            // El equipo puede actualizarse a veces, debe existir y no estar dado de baja
            'equipo_id' => ['sometimes', 'required', Rule::exists('equipos', 'id')->whereNot('estado', 'dado_de_baja')],
            // El técnico debe existir en la BD
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            // El tipo de mantenimiento permitido
            'tipo' => ['sometimes', 'required', Rule::in(['correctivo', 'preventivo'])],
            'descripcion' => ['sometimes', 'required', 'string'],
            'diagnostico' => ['nullable', 'string'],
            'accion' => ['nullable', 'string'],
            'fecha' => ['sometimes', 'required', 'date'],
            'proxima_fecha' => ['nullable', 'date'],
            // Estado del mantenimiento
            'estado' => ['nullable', Rule::in(['pendiente', 'en_proceso', 'finalizado'])],
            // Componentes que se evalúan en el mantenimiento
            'componentes' => ['sometimes', 'array'],
            'componentes.*' => [Rule::in(['bueno', 'regular', 'malo', 'reemplazado'])],
            // Evidencias fotográficas (máx. 40MB por foto)
            'fotos' => ['sometimes', 'array'],
            'fotos.*' => ['image', 'max:40960'],
            'costo' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados para las reglas definidas.
     */
    public function messages(): array
    {
        return [
            'equipo_id.required' => 'El campo equipo es obligatorio.',
            'equipo_id.exists' => 'El equipo seleccionado no es válido o ha sido dado de baja.',
            'user_id.required' => 'El campo técnico es obligatorio.',
            'user_id.exists' => 'El técnico seleccionado no es válido.',
            'tipo.required' => 'El campo tipo es obligatorio.',
            'tipo.in' => 'El tipo seleccionado no es válido.',
            'descripcion.required' => 'El campo descripción es obligatorio.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'fecha.date' => 'El campo fecha debe ser una fecha válida.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'fotos.*.image' => 'El archivo debe ser una imagen.',
            'fotos.*.max' => 'La imagen no debe pesar más de 40MB.',
            'costo.numeric' => 'El costo debe ser un valor numérico.',
            'costo.min' => 'El costo no puede ser negativo.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMantenimientoRequest extends FormRequest
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
            // El equipo es requerido, debe existir y no estar dado de baja
            'equipo_id'    => ['required', Rule::exists('equipos', 'id')->whereNot('estado', 'dado_de_baja')],
            // El técnico es requerido y debe existir en la base de datos
            'user_id'      => ['required', 'exists:users,id'],
            // Tipo de mantenimiento permitido: preventivo o correctivo
            'tipo'         => ['required', Rule::in(['correctivo', 'preventivo'])],
            'descripcion'  => ['required', 'string'],
            'diagnostico'  => ['nullable', 'string'],
            'accion'       => ['nullable', 'string'],
            'fecha'        => ['required', 'date'],
            // Próxima fecha es requerida para mantenimientos preventivos y debe ser posterior a la fecha de inicio
            'proxima_fecha'=> ['nullable', 'required_if:tipo,preventivo', 'date', 'after:fecha'],
            'costo'        => ['nullable', 'numeric', 'min:0'],
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
            'descripcion.required' => 'El campo descripción/actividad es obligatorio.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'fecha.date' => 'El campo fecha debe ser una fecha válida.',
            'proxima_fecha.required_if' => 'La próxima fecha es obligatoria para mantenimientos preventivos.',
            'proxima_fecha.date' => 'El campo próxima fecha debe ser una fecha válida.',
            'proxima_fecha.after' => 'La próxima fecha debe ser posterior a la fecha del mantenimiento.',
            'costo.numeric' => 'El costo debe ser un valor numérico.',
            'costo.min' => 'El costo no puede ser negativo.',
        ];
    }
}

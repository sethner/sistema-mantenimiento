<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfiguracionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_institucion' => ['nullable', 'string', 'max:255'],
            'director_nombre' => ['nullable', 'string', 'max:255'],
            'ruc' => ['nullable', 'string', 'max:11'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_institucion.max' => 'El nombre de la institución no debe exceder los 255 caracteres.',
            'director_nombre.max' => 'El nombre del director no debe exceder los 255 caracteres.',
            'ruc.max' => 'El RUC no debe exceder los 11 caracteres.',
            'direccion.max' => 'La dirección no debe exceder los 255 caracteres.',
            'telefono.max' => 'El teléfono no debe exceder los 20 caracteres.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'logo.image' => 'El archivo debe ser una imagen.',
            'logo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'logo.max' => 'La imagen no debe pesar más de 2MB.',
        ];
    }
}

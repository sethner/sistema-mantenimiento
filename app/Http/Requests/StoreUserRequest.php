<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Clase StoreUserRequest
 * Valida la información necesaria para el registro de un nuevo usuario en la plataforma.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación aplicables para la creación de un usuario.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            // Correo electrónico único en la tabla 'users'
            'email' => ['required', 'email', 'unique:users,email'],
            // Contraseña con validación fuerte de seguridad (min 8 caracteres, números, letras, símbolos)
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
            // El rol del usuario debe existir en la BD
            'role_id' => ['required', 'exists:roles,id'],
            // Foto de perfil opcional, máximo 2MB de peso
            'foto' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:2048'],
        ];
    }

    /**
     * Mensajes personalizados para los errores de validación.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El campo nombre debe ser una cadena de texto.',
            'name.max' => 'El campo nombre no debe exceder los 100 caracteres.',
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El campo correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está en uso.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role_id.required' => 'El campo rol es obligatorio.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La imagen debe ser de tipo: jpg, png, jpeg.',
            'foto.max' => 'La imagen no debe pesar más de 2MB.',
        ];
    }
}

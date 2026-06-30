<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Clase ProfileController
 * Maneja los ajustes personales del perfil del usuario (nombre, correo, cambio de credenciales y borrado de cuenta).
 */
class ProfileController extends Controller
{
    /**
     * Muestra el formulario para modificar la información del perfil del usuario actual.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Valida y actualiza los datos generales de la cuenta del usuario actual.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // Reiniciar la verificación del correo si es que este fue modificado
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('success', 'Perfil actualizado');
    }

    /**
     * Cierra la sesión y elimina definitivamente la cuenta del usuario actual (requiere contraseña actual).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validar contraseña actual por seguridad
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Cerrar sesión
        Auth::logout();

        // Borrar el registro
        $user->delete();

        // Invalidar tokens de sesión activos
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

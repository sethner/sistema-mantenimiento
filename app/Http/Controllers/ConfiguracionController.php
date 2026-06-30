<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateConfiguracionRequest;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Clase ConfiguracionController
 * Gestiona los datos institucionales globales y el logotipo utilizado para los informes.
 */
class ConfiguracionController extends Controller
{
    /**
     * Muestra la vista de configuración institucional con el registro existente (o uno en blanco).
     */
    public function index()
    {
        $config = Configuracion::first() ?? new Configuracion();
        return view('configuracion.index', compact('config'));
    }

    /**
     * Valida y actualiza los parámetros institucionales.
     * Si se carga un nuevo logo, remueve el logo institucional anterior del almacenamiento.
     */
    public function update(UpdateConfiguracionRequest $request)
    {
        $validated = $request->validated();

        $config = Configuracion::first() ?? new Configuracion();

        // Procesar y guardar el nuevo logo
        if ($request->hasFile('logo')) {
            if ($config->logo_path) {
                Storage::disk('public')->delete($config->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $config->logo_path = $path;
        }

        // Rellenar los campos de la institución exceptuando la imagen del logotipo
        $config->fill($request->except('logo'));
        $config->save();

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}

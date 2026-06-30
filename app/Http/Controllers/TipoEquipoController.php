<?php

namespace App\Http\Controllers;

use App\Models\TipoEquipo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

/**
 * Clase TipoEquipoController
 * Controla el catálogo de clasificación de equipos (ej. Servidores, PC Escritorio, Proyector).
 */
class TipoEquipoController extends Controller
{
    /**
     * Muestra el catálogo de tipos de equipos ordenados alfabéticamente.
     */
    public function index()
    {
        $tipos = TipoEquipo::orderBy('nombre')->get();

        return view('tipos_equipos.index', compact('tipos'));
    }

    /**
     * Muestra el formulario para crear un tipo de equipo.
     */
    public function create()
    {
        return view('tipos_equipos.create');
    }

    /**
     * Valida y registra un nuevo tipo de equipo en el catálogo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:tipo_equipos,nombre'],
            'imagen' => ['nullable', 'image', 'max:2048'], // Logo o icono descriptivo
        ]);

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('tipos_equipos', 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        TipoEquipo::create($validated);

        return redirect()->route('tipos-equipos.index')
            ->with('success', 'Tipo de equipo creado correctamente');
    }

    /**
     * Muestra la vista de edición de un tipo de equipo.
     */
    public function edit(TipoEquipo $tipos_equipo)
    {
        return view('tipos_equipos.edit', ['tipo' => $tipos_equipo]);
    }

    /**
     * Valida y actualiza los campos, reemplazando la imagen del tipo de equipo si se carga una nueva.
     */
    public function update(Request $request, TipoEquipo $tipos_equipo)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', Rule::unique('tipo_equipos', 'nombre')->ignore($tipos_equipo->id)],
            'imagen' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            if ($tipos_equipo->imagen) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $tipos_equipo->imagen));
            }
            $path = $request->file('imagen')->store('tipos_equipos', 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        $tipos_equipo->update($validated);

        return redirect()->route('tipos-equipos.index')
            ->with('success', 'Tipo de equipo actualizado correctamente');
    }

    /**
     * Elimina el tipo de equipo y su imagen asociada.
     */
    public function destroy(TipoEquipo $tipos_equipo)
    {
        if ($tipos_equipo->imagen) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $tipos_equipo->imagen));
        }
        $tipos_equipo->delete();

        return redirect()->route('tipos-equipos.index')
            ->with('success', 'Tipo de equipo eliminado correctamente');
    }
}

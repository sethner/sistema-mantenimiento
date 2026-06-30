<?php

namespace App\Http\Controllers;

use App\Models\CategoriaComponente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Clase CategoriaComponenteController
 * Controla el CRUD para las categorías a las cuales pertenecen los componentes de hardware.
 */
class CategoriaComponenteController extends Controller
{
    /**
     * Muestra el listado de todas las categorías disponibles ordenadas por nombre.
     */
    public function index()
    {
        $categorias = CategoriaComponente::orderBy('nombre')->get();

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Valida y registra una nueva categoría en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // El nombre es requerido, debe ser único en la tabla 'categorias_componentes' y máximo de 100 caracteres
            'nombre' => ['required', 'string', 'max:100', 'unique:categorias_componentes,nombre'],
        ]);

        CategoriaComponente::create($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoria creada correctamente');
    }

    /**
     * Muestra el formulario de edición de una categoría existente.
     */
    public function edit(CategoriaComponente $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Valida y actualiza los datos de la categoría en la base de datos.
     */
    public function update(Request $request, CategoriaComponente $categoria)
    {
        $validated = $request->validate([
            // Valida que sea única ignorando el registro actual
            'nombre' => ['required', 'string', 'max:100', Rule::unique('categorias_componentes', 'nombre')->ignore($categoria->id)],
        ]);

        $categoria->update($validated);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoria actualizada correctamente');
    }

    /**
     * Elimina físicamente la categoría seleccionada de la base de datos.
     */
    public function destroy(CategoriaComponente $categoria)
    {
        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoria eliminada correctamente');
    }
}

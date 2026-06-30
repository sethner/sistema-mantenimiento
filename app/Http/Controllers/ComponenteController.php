<?php

namespace App\Http\Controllers;

use App\Models\CategoriaComponente;
use App\Models\Componente;
use App\Models\TipoEquipo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

/**
 * Clase ComponenteController
 * Controla el catálogo general de componentes (repuestos del hardware, ej. RAM, Discos, Tarjetas de Red).
 */
class ComponenteController extends Controller
{
    /**
     * Muestra el catálogo de componentes cargando sus tipos y categorías.
     */
    public function index()
    {
        $componentes = Componente::with('tipo', 'categoria')
            ->orderBy('tipo_id')
            ->orderBy('nombre')
            ->get();

        return view('componentes.index', compact('componentes'));
    }

    /**
     * Muestra el formulario de creación de componentes.
     */
    public function create()
    {
        return view('componentes.create', [
            'tipos' => TipoEquipo::orderBy('nombre')->get(),
            'categorias' => CategoriaComponente::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Procesa y registra un nuevo componente en el catálogo.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_id' => ['required', 'exists:tipo_equipos,id'],
            'nombre' => [
                'required',
                'string',
                'max:100',
                // Validación para evitar duplicados en el mismo tipo de equipo
                Rule::unique('componentes', 'nombre')->where(fn ($query) => $query->where('tipo_id', $request->tipo_id)),
            ],
            'categoria_id' => ['required', 'exists:categorias_componentes,id'],
            'imagen' => ['nullable', 'image', 'max:2048'], // Máx 2MB
        ]);

        // Guardado físico del archivo de imagen
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('componentes', 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        Componente::create($validated);

        return redirect()->route('componentes.index')
            ->with('success', 'Componente registrado correctamente');
    }

    /**
     * Muestra el formulario para modificar un componente.
     */
    public function edit(Componente $componente)
    {
        return view('componentes.edit', [
            'componente' => $componente,
            'tipos' => TipoEquipo::orderBy('nombre')->get(),
            'categorias' => CategoriaComponente::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Valida y actualiza los datos del componente (borrando la imagen anterior si se sube una nueva).
     */
    public function update(Request $request, Componente $componente)
    {
        $validated = $request->validate([
            'tipo_id' => ['required', 'exists:tipo_equipos,id'],
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('componentes', 'nombre')
                    ->where(fn ($query) => $query->where('tipo_id', $request->tipo_id))
                    ->ignore($componente->id),
            ],
            'categoria_id' => ['required', 'exists:categorias_componentes,id'],
            'imagen' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('imagen')) {
            // Eliminar la imagen previa del almacenamiento
            if ($componente->imagen) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $componente->imagen));
            }
            $path = $request->file('imagen')->store('componentes', 'public');
            $validated['imagen'] = '/storage/' . $path;
        }

        $componente->update($validated);

        return redirect()->route('componentes.index')
            ->with('success', 'Componente actualizado correctamente');
    }

    /**
     * Visualiza los detalles técnicos del componente.
     */
    public function show(Componente $componente)
    {
        return view('componentes.show', compact('componente'));
    }

    /**
     * Elimina el componente de la base de datos.
     */
    public function destroy(Componente $componente)
    {
        $componente->delete();

        return redirect()->route('componentes.index')
            ->with('success', 'Componente eliminado correctamente');
    }
}

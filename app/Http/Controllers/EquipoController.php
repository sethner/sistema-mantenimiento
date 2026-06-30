<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipoRequest;
use App\Http\Requests\UpdateEquipoRequest;
use App\Models\CategoriaComponente;
use App\Models\Componente;
use App\Models\Equipo;
use App\Models\TipoEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Clase EquipoController
 * Gestiona el ciclo de vida de los equipos (baja, alta, edición, consulta y vinculación de componentes).
 */
class EquipoController extends Controller
{
    // Constantes para estados válidos de componentes y equipos en general
    private const ESTADOS_COMPONENTE = ['bueno', 'regular', 'malo', 'reemplazado'];
    private const ESTADOS_EQUIPO = ['operativo', 'en_mantenimiento', 'con_falla', 'dado_de_baja'];

    /**
     * Muestra la lista de equipos con paginación y filtros de búsqueda por nombre/código, estado y tipo.
     */
    public function index(Request $request)
    {
        $query = Equipo::with('tipo')->orderBy('nombre');

        // Búsqueda aproximada por nombre o código de bien
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        // Filtro por estado operativo/falla
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo de equipo
        if ($request->filled('tipo_id')) {
            $query->where('tipo_id', $request->tipo_id);
        }

        $equipos = $query->paginate(10)->withQueryString();
        $estados = self::ESTADOS_EQUIPO;
        $tipos = TipoEquipo::orderBy('nombre')->get();

        return view('equipos.index', compact('equipos', 'estados', 'tipos'));
    }

    /**
     * Muestra el formulario para registrar un equipo nuevo.
     */
    public function create()
    {
        $tipos = TipoEquipo::orderBy('nombre')->get();

        return view('equipos.create', compact('tipos'));
    }

    /**
     * Registra un nuevo equipo e instala de manera automática los componentes por defecto asociados a su Tipo de Equipo.
     */
    public function store(StoreEquipoRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $equipo = Equipo::create([
                ...$validated,
                'estado' => $validated['estado'] ?? 'operativo',
            ]);

            // Obtener componentes registrados para este tipo de equipo
            $componentes = Componente::where('tipo_id', $validated['tipo_id'])->get();

            // Vincular automáticamente los componentes iniciales como "bueno"
            foreach ($componentes as $componente) {
                $equipo->componentes()->attach($componente->id, [
                    'estado' => 'bueno',
                ]);
            }

            DB::commit();

            return redirect()->route('equipos.index')
                ->with('success', 'Equipo registrado con componentes automaticamente');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Error al registrar equipo: '.$e->getMessage());
        }
    }

    /**
     * Muestra el formulario de edición de un equipo.
     */
    public function edit(Equipo $equipo)
    {
        $tipos = TipoEquipo::orderBy('nombre')->get();

        return view('equipos.edit', compact('equipo', 'tipos'));
    }

    /**
     * Actualiza el equipo en la base de datos.
     * Si el Tipo de Equipo es modificado, se remueven los componentes anteriores y se instalan los del nuevo tipo.
     */
    public function update(UpdateEquipoRequest $request, Equipo $equipo)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $tipoAnterior = $equipo->tipo_id;

            $equipo->update($validated);

            // Re-vincular componentes si cambió el tipo de equipo
            if ((int) $tipoAnterior !== (int) $validated['tipo_id']) {
                $equipo->componentes()->detach();

                $componentes = Componente::where('tipo_id', $validated['tipo_id'])->get();

                foreach ($componentes as $componente) {
                    $equipo->componentes()->attach($componente->id, [
                        'estado' => 'bueno',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('equipos.index')
                ->with('success', 'Equipo actualizado correctamente');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Elimina físicamente el equipo del sistema.
     */
    public function destroy(Equipo $equipo)
    {
        $equipo->delete();

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo eliminado correctamente');
    }

    /**
     * Muestra el detalle del equipo, su historial técnico y componentes activos.
     */
    public function show(Equipo $equipo)
    {
        $equipo->load('tipo', 'componentes.categoria', 'mantenimientos.usuario', 'historialFallas.componente');
        $componentes = Componente::with('categoria')->orderBy('nombre')->get();

        return view('equipos.show', compact('equipo', 'componentes'));
    }

    /**
     * Vincula manualmente un componente adicional o personalizado a un equipo.
     */
    public function agregarComponente(Request $request, Equipo $equipo)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
        ]);

        $nombre = ucfirst(strtolower(trim($validated['nombre'])));
        $categoria = CategoriaComponente::firstOrCreate(['nombre' => 'Interno']);

        // Crear el componente si no existe para este tipo de equipo
        $componente = Componente::firstOrCreate(
            [
                'tipo_id' => $equipo->tipo_id,
                'nombre' => $nombre,
            ],
            [
                'categoria_id' => $categoria->id,
            ]
        );

        // Validar si ya está asignado para evitar duplicados
        if ($equipo->componentes()->where('componente_id', $componente->id)->exists()) {
            return back()->with('error', 'El componente ya esta asignado');
        }

        $equipo->componentes()->attach($componente->id, [
            'estado' => 'bueno',
        ]);

        return back()->with('success', 'Componente agregado correctamente');
    }

    /**
     * Remueve la vinculación de un componente sobre el equipo.
     */
    public function quitarComponente(Equipo $equipo, Componente $componente)
    {
        $equipo->componentes()->detach($componente->id);

        return back()->with('success', 'Componente eliminado');
    }

    /**
     * Cambia manualmente el estado de conservación de un componente dentro del equipo.
     */
    public function cambiarEstado(Request $request, Equipo $equipo, Componente $componente)
    {
        $validated = $request->validate([
            'estado' => ['required', Rule::in(self::ESTADOS_COMPONENTE)],
        ]);

        $equipo->componentes()->updateExistingPivot($componente->id, [
            'estado' => $validated['estado'],
        ]);

        return back()->with('success', 'Estado actualizado');
    }
}

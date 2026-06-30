<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMantenimientoRequest;
use App\Http\Requests\UpdateMantenimientoRequest;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Notificacion;
use App\Models\MantenimientoFoto;
use App\Models\HistorialFalla;
use Illuminate\Support\Facades\Storage;
use App\Notifications\MantenimientoAsignado;
use App\Notifications\MantenimientoRegistradoAdmin;
use App\Notifications\FallaCriticaAlerta;
use App\Notifications\MantenimientoFinalizado;

class MantenimientoController extends Controller
{
    private const ESTADOS_MANTENIMIENTO = ['pendiente', 'en_proceso', 'finalizado'];
    private const ESTADOS_COMPONENTE = ['bueno', 'regular', 'malo', 'reemplazado'];
    private const TIPOS_MANTENIMIENTO = ['correctivo', 'preventivo'];

    public function index(Request $request)
    {
        $query = Mantenimiento::with('equipo', 'usuario');

        // 🔥 Filtro para técnicos: solo ver sus mantenimientos y solo los que no están finalizados
        if (auth()->user()->hasRole('tecnico')) {
            $query->where('user_id', auth()->id())
                  ->where('estado', '!=', 'finalizado');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('equipo', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        $mantenimientos = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mantenimientos.index', compact('mantenimientos'));
    }

    /**
     * Muestra el formulario para registrar un nuevo mantenimiento.
     */
    public function create()
    {
        return view('mantenimientos.create', [
            // Solo muestra los equipos activos, excluyendo los dados de baja
            'equipos' => Equipo::where('estado', '!=', 'dado_de_baja')->orderBy('nombre')->get(),
            'usuarios' => $this->tecnicos(),
        ]);
    }

    public function store(StoreMantenimientoRequest $request)
    {
        $validated = $request->validated();

        if ($validated['tipo'] === 'correctivo') {
            $validated['proxima_fecha'] = null;
        }

        // 🔥 Si es técnico, forzar que se asigne a sí mismo
        if (auth()->user()->hasRole('tecnico')) {
            $validated['user_id'] = auth()->id();
        } else {
            $this->assertTecnico((int) $validated['user_id']);
        }

        $validated['estado'] = 'pendiente';

        $mantenimiento = Mantenimiento::create($validated);

        $this->sincronizarEstadoEquipo($mantenimiento);

        // 🔥 NOTIFICACIÓN DE ASIGNACIÓN (Database + Email)
        $tecnico = User::find($mantenimiento->user_id);
        if ($tecnico) {
            $tecnico->notify(new MantenimientoAsignado($mantenimiento));
        }

        // 🔥 NOTIFICAR A ADMINISTRADORES (Aviso de registro)
        $admins = User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(nombre) = ?', ['administrador']))->get();
        foreach ($admins as $admin) {
            // No enviar doble si el admin es el mismo técnico
            if ($admin->id !== $tecnico?->id) {
                $admin->notify(new MantenimientoRegistradoAdmin($mantenimiento));
            }
        }

        return redirect()->route('mantenimientos.index')
            ->with('success', 'Mantenimiento registrado y técnico notificado');
    }

    public function edit(Mantenimiento $mantenimiento)
    {
        // 🔥 Si es técnico y el mantenimiento ya terminó, no puede editar
        if (auth()->user()->hasRole('tecnico') && $mantenimiento->estado === 'finalizado') {
            return redirect()->route('mantenimientos.index')
                ->with('error', 'Este mantenimiento ya ha sido finalizado.');
        }

        return view('mantenimientos.edit', [
            'mantenimiento' => $mantenimiento,
            'equipos' => Equipo::orderBy('nombre')->get(),
            'usuarios' => $this->tecnicos(),
        ]);
    }

    /**
     * Actualiza el mantenimiento en la base de datos (utilizado tanto para guardados parciales como finales).
     */
    public function update(UpdateMantenimientoRequest $request, Mantenimiento $mantenimiento)
    {
        $validated = $request->validated();
        \Illuminate\Support\Facades\Log::info('fotos update request:', ['all' => $request->all(), 'files' => $request->allFiles()]);

        if (isset($validated['user_id'])) {
            $this->assertTecnico((int) $validated['user_id']);
        }

        // Ejecutar toda la actualización dentro de una transacción para evitar inconsistencias
        DB::transaction(function () use ($mantenimiento, $validated, $request) {
            $oldEstado = $mantenimiento->estado;

            $data = collect($validated)
                ->only([
                    'equipo_id',
                    'user_id',
                    'tipo',
                    'descripcion',
                    'diagnostico',
                    'accion',
                    'fecha',
                    'proxima_fecha',
                    'costo',
                ])
                ->all();

            // Si es técnico, forzar que sea su ID por seguridad
            if (auth()->user()->hasRole('tecnico')) {
                $data['user_id'] = auth()->id();
            } elseif (isset($data['user_id'])) {
                $this->assertTecnico((int) $data['user_id']);
            }

            if ($request->has('estado')) {
                $data['estado'] = $validated['estado'];
            }

            $mantenimiento->update($data);

            // Manejo y guardado de las fotos subidas como evidencias
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $file) {
                    // Almacenar el archivo en el disco público dentro del directorio 'evidencias'
                    $path = $file->store('evidencias', 'public');
                    // Crear el registro de la foto asociada al mantenimiento
                    $mantenimiento->fotos()->create([
                        'ruta'             => $path,
                        'nombre_original'  => $file->getClientOriginalName(),
                    ]);
                }
            }

            // Actualización de los estados individuales de los componentes vinculados
            if (isset($validated['componentes'])) {
                $mantenimiento->loadMissing('equipo.componentes');
                $componentesAsignados = $mantenimiento->equipo->componentes->pluck('id')->all();

                foreach ($validated['componentes'] as $componenteId => $estado) {
                    if (in_array((int) $componenteId, $componentesAsignados, true)) {
                        $mantenimiento->equipo->componentes()->updateExistingPivot($componenteId, [
                            'estado' => $estado,
                        ]);

                        // Si el componente tiene estado "malo", se registra una falla crítica en el historial
                        if ($estado === 'malo') {
                            HistorialFalla::updateOrCreate(
                                [
                                    'equipo_id'       => $mantenimiento->equipo_id,
                                    'mantenimiento_id' => $mantenimiento->id,
                                    'componente_id'   => $componenteId,
                                ],
                                [
                                    'descripcion' => 'Falla detectada en: '
                                        . $mantenimiento->equipo->componentes->find($componenteId)->nombre,
                                    'tipo'  => $mantenimiento->tipo,
                                    'fecha' => now(),
                                ]
                            );
                        }
                    }
                }
            }

            $freshMantenimiento = $mantenimiento->fresh();
            // Sincronizar el estado general del equipo con base al estado del mantenimiento actual
            $this->sincronizarEstadoEquipo($freshMantenimiento);

            // Notificar a los administradores si el mantenimiento cambió al estado finalizado
            if ($oldEstado !== 'finalizado' && $freshMantenimiento->estado === 'finalizado') {
                $admins = User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(nombre) = ?', ['administrador']))->get();
                foreach ($admins as $admin) {
                    $admin->notify(new MantenimientoFinalizado($freshMantenimiento));
                }
            }
        });

        return redirect()->route('mantenimientos.show', $mantenimiento)
            ->with('success', 'Mantenimiento actualizado');
    }

    public function iniciar(Mantenimiento $mantenimiento)
    {
        // Seguridad: solo el técnico asignado puede iniciar
        if (auth()->user()->hasRole('tecnico') && $mantenimiento->user_id !== auth()->id()) {
            abort(403);
        }

        $mantenimiento->update(['estado' => 'en_proceso']);
        $this->sincronizarEstadoEquipo($mantenimiento);

        return redirect()->route('mantenimientos.show', $mantenimiento)
            ->with('success', 'Mantenimiento iniciado. Registre el diagnóstico y acciones.');
    }

    public function destroy(Mantenimiento $mantenimiento)
    {
        $mantenimiento->delete();

        return redirect()->route('mantenimientos.index')
            ->with('success', 'Mantenimiento eliminado');
    }

    /**
     * Elimina permanentemente una foto de evidencia física y su registro de la base de datos.
     */
    public function eliminarFoto(MantenimientoFoto $foto)
    {
        // Seguridad: si es técnico, solo puede eliminar si la orden le pertenece
        if (auth()->user()->hasRole('tecnico') && $foto->mantenimiento->user_id !== auth()->id()) {
            abort(403);
        }

        // Eliminar físicamente del storage
        if (Storage::disk('public')->exists($foto->ruta)) {
            Storage::disk('public')->delete($foto->ruta);
        }

        // Eliminar de la base de datos
        $foto->delete();

        return back()->with('success', 'Evidencia eliminada correctamente.');
    }

    public function show(Mantenimiento $mantenimiento)
    {
        // 🔥 Si es técnico y el mantenimiento ya terminó, no debería ver el show (según pedido)
        if (auth()->user()->hasRole('tecnico') && $mantenimiento->estado === 'finalizado') {
            return redirect()->route('mantenimientos.index')
                ->with('error', 'El mantenimiento finalizado ya no está disponible para vista técnica.');
        }

        $mantenimiento->load('equipo.tipo', 'equipo.componentes', 'usuario');

        return view('mantenimientos.show', compact('mantenimiento'));
    }

    private function tecnicos()
    {
        $query = User::whereHas('roles', function ($query) {
            $query->whereRaw('LOWER(nombre) = ?', ['tecnico']);
        });

        // 🔥 Si el usuario logueado es técnico, solo puede verse a sí mismo
        if (auth()->user()->hasRole('tecnico')) {
            $query->where('id', auth()->id());
        }

        return $query->orderBy('name')->get();
    }

    private function assertTecnico(int $userId): void
    {
        $user = User::findOrFail($userId);

        if (! $user->hasRole('tecnico')) {
            throw ValidationException::withMessages([
                'user_id' => 'El usuario seleccionado debe tener rol tecnico.',
            ]);
        }
    }

    private function sincronizarEstadoEquipo(Mantenimiento $mantenimiento): void
    {
        $equipo = $mantenimiento->equipo;

        if (! $equipo || $equipo->estado === 'dado_de_baja') {
            return;
        }

        $nuevoEstado = match ($mantenimiento->estado) {
            'pendiente', 'en_proceso' => $mantenimiento->tipo === 'correctivo'
                ? 'con_falla'
                : 'en_mantenimiento',
            'finalizado' => 'operativo',
            default => $equipo->estado,
        };

        $equipo->update(['estado' => $nuevoEstado]);
        
        // 🔥 CALCULAR PRÓXIMO MANTENIMIENTO PREVENTIVO
        if ($mantenimiento->estado === 'finalizado') {
            $equipo->update([
                'proximo_mantenimiento' => now()->addMonths($equipo->frecuencia_mantenimiento ?? 6)
            ]);
        }

        // 🔥 REGISTRAR EN HISTORIAL SI ES FALLA
        if ($nuevoEstado === 'con_falla') {
            HistorialFalla::updateOrCreate([
                'equipo_id' => $equipo->id,
                'mantenimiento_id' => $mantenimiento->id,
                'componente_id' => null,
            ], [
                'descripcion' => $mantenimiento->descripcion,
                'tipo' => $mantenimiento->tipo,
                'fecha' => now(),
            ]);

            // Notificar a los administradores (Database + Email)
            $admins = User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(nombre) = ?', ['administrador']))->get();
            foreach ($admins as $admin) {
                $admin->notify(new FallaCriticaAlerta($equipo));
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;

/**
 * Clase CargaTecnicoController
 * Administra el panel de visualización de carga de trabajo de los técnicos para los administradores.
 */
class CargaTecnicoController extends Controller
{
    /**
     * Muestra el listado de técnicos y la distribución de sus tareas.
     * Retorna la carga agrupada por tareas pendientes, en proceso, finalizadas y vencidas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener todos los usuarios con rol técnico y contar sus órdenes asignadas
        $tecnicos = \App\Models\User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(nombre) = ?', ['tecnico']))
            ->with(['mantenimientos' => fn($q) => $q->select('id', 'user_id', 'estado', 'fecha')])
            ->get()
            ->map(function ($t) {
                $mantenimientos = $t->mantenimientos;
                return [
                    'id'         => $t->id,
                    'nombre'     => $t->name,
                    'foto'       => $t->foto,
                    'pendientes' => $mantenimientos->where('estado', 'pendiente')->count(),
                    'en_proceso' => $mantenimientos->where('estado', 'en_proceso')->count(),
                    'finalizados'=> $mantenimientos->where('estado', 'finalizado')->count(),
                    'total'      => $mantenimientos->count(),
                    // Filtrar tareas no completadas cuya fecha programada sea anterior a hoy
                    'vencidos'   => $mantenimientos
                        ->whereIn('estado', ['pendiente', 'en_proceso'])
                        ->filter(fn($m) => $m->fecha && \Carbon\Carbon::parse($m->fecha)->isBefore(today()))
                        ->count(),
                ];
            })
            // Ordenar de mayor a menor según la cantidad total de tareas asignadas
            ->sortByDesc('total')
            ->values();

        return view('carga-tecnico.index', compact('tecnicos'));
    }
}

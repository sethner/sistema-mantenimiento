<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mantenimiento;
use App\Models\User;
use App\Notifications\MantenimientoVencidoNotification;

class CheckMantenimientosVencidos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mantenimiento:check-vencidos';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Revisa mantenimientos programados vencidos (no finalizados) y envía notificaciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Traer mantenimientos pendientes o en proceso que vencieron antes de hoy
        $mantenimientos = Mantenimiento::with('equipo', 'usuario')
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->whereDate('fecha', '<', today())
            ->get();

        $admins = User::whereHas('roles', function($q) {
            $q->whereRaw('LOWER(nombre) = ?', ['administrador']);
        })->get();

        $notificadosCount = 0;

        foreach ($mantenimientos as $mantenimiento) {
            // 1. Notificar al técnico asignado (si tiene)
            $tecnico = $mantenimiento->usuario;
            if ($tecnico) {
                $tecnico->notify(new MantenimientoVencidoNotification($mantenimiento));
            }

            // 2. Notificar a todos los administradores (evitando duplicar si el técnico asignado es admin)
            foreach ($admins as $admin) {
                if (!$tecnico || $admin->id !== $tecnico->id) {
                    $admin->notify(new MantenimientoVencidoNotification($mantenimiento));
                }
            }

            $notificadosCount++;
        }

        $this->info("Chequeo completado. Se enviaron alertas para {$notificadosCount} mantenimientos vencidos.");
    }
}

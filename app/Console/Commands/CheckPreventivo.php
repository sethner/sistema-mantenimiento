<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPreventivo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mantenimiento:check-preventivo';
    protected $description = 'Revisa equipos próximos a mantenimiento preventivo y notifica';

    public function handle()
    {
        // Traer equipos que vencen en los próximos 7 días o ya vencieron
        $equipos = \App\Models\Equipo::where('proximo_mantenimiento', '<=', now()->addDays(7))
            ->where('estado', 'operativo')
            ->get();

        $admins = \App\Models\User::whereHas('roles', function($q) {
            $q->whereRaw('LOWER(nombre) = ?', ['administrador']);
        })->get();

        foreach ($equipos as $equipo) {
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\MantenimientoPreventivoNotification($equipo));
            }
        }

        $this->info('Chequeo completado. Se enviaron notificaciones para ' . $equipos->count() . ' equipos.');
    }
}

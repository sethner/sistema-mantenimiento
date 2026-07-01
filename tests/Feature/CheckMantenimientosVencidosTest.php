<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\TipoEquipo;
use App\Models\User;
use App\Notifications\MantenimientoVencidoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckMantenimientosVencidosTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_detects_overdue_mantenimientos_and_sends_notifications(): void
    {
        Notification::fake();

        // 1. Setup users
        $tecnico = $this->userWithRole('tecnico');
        $admin = $this->userWithRole('administrador');

        // 2. Setup equipment and maintenance
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-301',
            'nombre' => 'Equipo Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        // Overdue maintenance (fecha in the past)
        $mantenimientoVencido = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Revisión vencida',
            'fecha' => today()->subDays(2),
            'estado' => 'pendiente',
        ]);

        // Normal maintenance (fecha in the future)
        $mantenimientoFuturo = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Revisión futura',
            'fecha' => today()->addDays(2),
            'estado' => 'pendiente',
        ]);

        // Executing artisan command
        $this->artisan('mantenimiento:check-vencidos')
            ->expectsOutput('Chequeo completado. Se enviaron alertas para 1 mantenimientos vencidos.')
            ->assertExitCode(0);

        // Assert notification sent to assigned technician
        Notification::assertSentTo(
            $tecnico,
            MantenimientoVencidoNotification::class,
            function ($notification) use ($mantenimientoVencido, $tecnico) {
                return $notification->toDatabase($tecnico)['tipo'] === 'alerta_vencido';
            }
        );

        // Assert notification sent to admin has null enlace
        Notification::assertSentTo(
            $admin,
            MantenimientoVencidoNotification::class,
            function ($notification) use ($admin) {
                return $notification->toDatabase($admin)['enlace'] === null;
            }
        );

        // Assert notification was not sent for future maintenance
        Notification::assertSentToTimes($tecnico, MantenimientoVencidoNotification::class, 1);
        Notification::assertSentToTimes($admin, MantenimientoVencidoNotification::class, 1);
    }
}

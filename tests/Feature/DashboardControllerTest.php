<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_redirects_by_role(): void
    {
        $admin = $this->userWithRole('administrador');
        $tecnico = $this->userWithRole('tecnico');
        $supervisor = $this->userWithRole('supervisor');

        // Admin
        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.admin');

        // Tecnico
        $this->actingAs($tecnico)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.tecnico');

        // Supervisor
        $this->actingAs($supervisor)
            ->get(route('dashboard'))
            ->assertRedirect(route('reportes.index'));
    }

    public function test_non_admin_cannot_access_dashboard_data(): void
    {
        $tecnico = $this->userWithRole('tecnico');

        $this->actingAs($tecnico)
            ->get(route('dashboard.data'))
            ->assertForbidden();
    }

    public function test_admin_can_access_dashboard_data_and_filter(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = \App\Models\TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-501',
            'nombre' => 'PC Laboratorio',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        // Create 2 maintenance records with different properties
        $m1 = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $admin->id, // acts as technical user here (wait, user must have role tecnico in validation? No, in DB it's fine)
            'tipo' => 'correctivo',
            'descripcion' => 'Mantenimiento Correctivo',
            'fecha' => '2026-06-01',
            'estado' => 'finalizado',
            'costo' => 100,
        ]);
        
        $m2 = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $admin->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Mantenimiento Preventivo',
            'fecha' => '2026-06-15',
            'estado' => 'pendiente',
            'costo' => 50,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.data', [
                'fecha_inicio' => '2026-06-01',
                'fecha_fin' => '2026-06-10',
                'tipo' => 'correctivo',
                'estado' => 'finalizado',
            ]));

        $response->assertOk()
            ->assertJsonStructure([
                'kpis' => [
                    'totalEquipos',
                    'equiposOperativos',
                    'equiposConFalla',
                    'mantenimientosPendientes',
                    'inversionAnual',
                    'inversionMensual',
                    'isFiltered'
                ],
                'charts' => [
                    'mantenimientosPorMes',
                    'inversionPorMes',
                    'estadoEquipos',
                    'mantenimientosPorTipo'
                ]
            ]);

        $data = $response->json();
        // Since we filtered for correctivo and finalizado and date June 1st to 10th:
        // Only $m1 matches!
        $this->assertEquals(100, $data['kpis']['inversionAnual']);
        $this->assertTrue($data['kpis']['isFiltered']);
    }
}

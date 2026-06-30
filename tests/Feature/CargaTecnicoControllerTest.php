<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CargaTecnicoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_carga_tecnico(): void
    {
        $tecnico = $this->userWithRole('tecnico');

        $this->actingAs($tecnico)
            ->get(route('carga-tecnico.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_carga_tecnico_dashboard_sorted_by_total(): void
    {
        $admin = $this->userWithRole('administrador');
        
        $tecnico1 = $this->userWithRole('tecnico');
        $tecnico1->update(['name' => 'Tecnico Uno']);
        
        $tecnico2 = $this->userWithRole('tecnico');
        $tecnico2->update(['name' => 'Tecnico Dos']);

        $tipo = \App\Models\TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-401',
            'nombre' => 'PC Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        // Tecnico 1: 1 maintenance
        Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico1->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Mantenimiento 1',
            'fecha' => now()->toDateString(),
            'estado' => 'pendiente',
        ]);

        // Tecnico 2: 2 maintenances (1 is overdue/vencido)
        Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico2->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Mantenimiento 2',
            'fecha' => now()->toDateString(),
            'estado' => 'pendiente',
        ]);
        Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico2->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Mantenimiento 3 Vencido',
            'fecha' => now()->subDays(5)->toDateString(), // Overdue
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('carga-tecnico.index'));

        $response->assertOk()
            ->assertViewIs('carga-tecnico.index')
            ->assertSee('Tecnico Dos')
            ->assertSee('Tecnico Uno');

        // Verify the order: Tecnico Dos has 2 tasks, Tecnico Uno has 1 task.
        // Let's assert variables in view
        $tecnicos = $response->viewData('tecnicos');
        $this->assertCount(2, $tecnicos);
        
        // Sorted by total tasks desc
        $this->assertEquals($tecnico2->id, $tecnicos[0]['id']);
        $this->assertEquals($tecnico1->id, $tecnicos[1]['id']);

        // Check columns mapping
        $this->assertEquals(2, $tecnicos[0]['total']);
        $this->assertEquals(1, $tecnicos[0]['vencidos']); // one overdue
    }
}

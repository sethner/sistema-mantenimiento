<?php

namespace Tests\Feature;

use App\Models\CategoriaComponente;
use App\Models\Componente;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\Role;
use App\Models\TipoEquipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemaAipTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_tecnico_and_supervisor_access_is_enforced(): void
    {
        $admin = $this->userWithRole('administrador');
        $tecnico = $this->userWithRole('tecnico');
        $supervisor = $this->userWithRole('supervisor');

        $this->actingAs($admin)
            ->get(route('equipos.index'))
            ->assertOk();

        $this->actingAs($tecnico)
            ->get(route('mantenimientos.index'))
            ->assertOk();

        $this->actingAs($tecnico)
            ->get(route('equipos.index'))
            ->assertForbidden();

        $this->actingAs($supervisor)
            ->get(route('mantenimientos.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_component_with_category(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'Laptop']);
        $categoria = CategoriaComponente::create(['nombre' => 'Interno']);

        $this->actingAs($admin)
            ->post(route('componentes.store'), [
                'tipo_id' => $tipo->id,
                'nombre' => 'RAM',
                'categoria_id' => $categoria->id,
            ])
            ->assertRedirect(route('componentes.index'));

        $this->assertDatabaseHas('componentes', [
            'tipo_id' => $tipo->id,
            'nombre' => 'RAM',
            'categoria_id' => $categoria->id,
        ]);
    }

    public function test_mantenimiento_attention_persists_diagnosis_action_and_component_state(): void
    {
        $admin = $this->userWithRole('administrador');
        $tecnico = $this->userWithRole('tecnico');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $categoria = CategoriaComponente::create(['nombre' => 'Interno']);
        $componente = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Disco duro',
        ]);
        $equipo = Equipo::create([
            'codigo' => 'AIP-001',
            'nombre' => 'PC Laboratorio',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);
        $equipo->componentes()->attach($componente->id, ['estado' => 'bueno']);

        $this->actingAs($admin)
            ->post(route('mantenimientos.store'), [
                'equipo_id' => $equipo->id,
                'user_id' => $tecnico->id,
                'tipo' => 'correctivo',
                'descripcion' => 'Equipo lento',
                'fecha' => '2026-04-27',
                'estado' => 'pendiente',
            ])
            ->assertRedirect(route('mantenimientos.index'));

        $mantenimiento = Mantenimiento::firstOrFail();
        $this->assertSame('con_falla', $equipo->refresh()->estado);

        $this->actingAs($admin)
            ->put(route('mantenimientos.update', $mantenimiento), [
                'diagnostico' => 'Falla en disco',
                'accion' => 'Se reemplazo el disco',
                'estado' => 'finalizado',
                'componentes' => [
                    $componente->id => 'reemplazado',
                ],
            ])
            ->assertRedirect(route('mantenimientos.show', $mantenimiento));

        $this->assertDatabaseHas('mantenimientos', [
            'id' => $mantenimiento->id,
            'diagnostico' => 'Falla en disco',
            'accion' => 'Se reemplazo el disco',
            'estado' => 'finalizado',
        ]);

        $this->assertSame('operativo', $equipo->refresh()->estado);

        $this->assertDatabaseHas('equipo_componentes', [
            'equipo_id' => $equipo->id,
            'componente_id' => $componente->id,
            'estado' => 'reemplazado',
        ]);
    }

    public function test_dashboard_and_reports_show_preventive_alerts_and_statistics(): void
    {
        $admin = $this->userWithRole('administrador');
        $tecnico = $this->userWithRole('tecnico');
        $tipo = TipoEquipo::create(['nombre' => 'Proyector']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-002',
            'nombre' => 'Proyector Aula',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Limpieza programada',
            'fecha' => now()->subDay()->toDateString(),
            'estado' => 'pendiente',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Preventivos Vencidos')
            ->assertSee('Proyector Aula');

        $this->actingAs($admin)
            ->get(route('reportes.index'))
            ->assertOk()
            ->assertSee('Centro de Reportes')
            ->assertSee('Mantenimiento');
    }

}

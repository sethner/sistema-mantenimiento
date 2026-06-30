<?php

namespace Tests\Feature;

use App\Models\Configuracion;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReporteControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createSeedData()
    {
        $tipo = \App\Models\TipoEquipo::create(['nombre' => 'Laptop']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-601',
            'nombre' => 'Laptop Docente',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);
        $tecnico = $this->userWithRole('tecnico');

        Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Revisión periódica',
            'fecha' => '2026-06-20',
            'estado' => 'finalizado',
            'costo' => 80.00,
        ]);

        Configuracion::create([
            'nombre_institucion' => 'Institución de Prueba',
            'director_nombre' => 'Director de Prueba',
        ]);

        return [$equipo, $tecnico];
    }

    public function test_supervisor_and_admin_can_access_reportes_index(): void
    {
        $this->createSeedData();
        $admin = $this->userWithRole('administrador');
        $supervisor = $this->userWithRole('supervisor');

        $this->actingAs($admin)
            ->get(route('reportes.index'))
            ->assertOk()
            ->assertViewIs('reportes.index');

        $this->actingAs($supervisor)
            ->get(route('reportes.index'))
            ->assertOk()
            ->assertViewIs('reportes.index');
    }

    public function test_descargar_ficha_bienes(): void
    {
        list($equipo) = $this->createSeedData();
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->get(route('reportes.bienes.pdf', ['equipo_id' => $equipo->id]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_descargar_reporte_tecnico(): void
    {
        list($equipo, $tecnico) = $this->createSeedData();
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->get(route('reportes.tecnico.pdf', [
                'tecnico_id' => $tecnico->id,
                'fecha_inicio' => '2026-06-01',
                'fecha_fin' => '2026-06-30'
            ]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_descargar_reporte_mantenimientos(): void
    {
        list($equipo) = $this->createSeedData();
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->get(route('reportes.mantenimientos.pdf', [
                'equipo_id' => $equipo->id,
                'fecha_inicio' => '2026-06-01',
                'fecha_fin' => '2026-06-30'
            ]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_descargar_dashboard_executive_summary(): void
    {
        $this->createSeedData();
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->get(route('reportes.dashboard'));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_descargar_reporte_baja(): void
    {
        $tipo = \App\Models\TipoEquipo::create(['nombre' => 'Laptop']);
        Equipo::create([
            'codigo' => 'AIP-602',
            'nombre' => 'Laptop Antigua',
            'tipo_id' => $tipo->id,
            'estado' => 'dado_de_baja',
        ]);
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->get(route('reportes.baja.pdf'));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_descargar_reporte_inversion(): void
    {
        $this->createSeedData();
        $admin = $this->userWithRole('administrador');

        // Validation test: anio required
        $this->actingAs($admin)
            ->get(route('reportes.inversion.pdf', ['anio' => '']))
            ->assertSessionHasErrors('anio');

        // Successful download
        $response = $this->actingAs($admin)
            ->get(route('reportes.inversion.pdf', ['anio' => 2026, 'mes' => 6]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}

<?php

namespace Tests\Feature;

use App\Models\CategoriaComponente;
use App\Models\Componente;
use App\Models\Equipo;
use App\Models\Mantenimiento;
use App\Models\MantenimientoFoto;
use App\Models\TipoEquipo;
use App\Models\User;
use App\Notifications\FallaCriticaAlerta;
use App\Notifications\MantenimientoAsignado;
use App\Notifications\MantenimientoFinalizado;
use App\Notifications\MantenimientoRegistradoAdmin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MantenimientoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tecnico_only_sees_their_unfinished_mantenimientos_in_index(): void
    {
        $tecnico = $this->userWithRole('tecnico');
        $otroTecnico = $this->userWithRole('tecnico');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-301',
            'nombre' => 'Equipo Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        // Assigned to tecnico, pending -> should see
        $m1 = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Revisión 1',
            'fecha' => '2026-06-28',
            'estado' => 'pendiente',
        ]);

        // Assigned to tecnico, finished -> should NOT see
        $m2 = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Revisión 2',
            'fecha' => '2026-06-28',
            'estado' => 'finalizado',
        ]);

        // Assigned to other tecnico -> should NOT see
        $m3 = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $otroTecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Revisión 3',
            'fecha' => '2026-06-28',
            'estado' => 'pendiente',
        ]);

        $this->actingAs($tecnico)
            ->get(route('mantenimientos.index'))
            ->assertOk()
            ->assertSee('Revisión 1')
            ->assertDontSee('Revisión 2')
            ->assertDontSee('Revisión 3');
    }

    public function test_admin_sees_all_mantenimientos_in_index(): void
    {
        $admin = $this->userWithRole('administrador');
        $tecnico = $this->userWithRole('tecnico');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-302',
            'nombre' => 'Equipo Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Revisión 1',
            'fecha' => '2026-06-28',
            'estado' => 'pendiente',
        ]);
        Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Revisión 2',
            'fecha' => '2026-06-28',
            'estado' => 'finalizado',
        ]);

        $this->actingAs($admin)
            ->get(route('mantenimientos.index'))
            ->assertOk()
            ->assertSee('AIP-302')
            ->assertSee('Equipo Test');
    }

    public function test_admin_can_store_mantenimiento_and_notify_users(): void
    {
        Notification::fake();

        $admin = $this->userWithRole('administrador');
        $tecnico = $this->userWithRole('tecnico');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-303',
            'nombre' => 'Equipo Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('mantenimientos.store'), [
                'equipo_id' => $equipo->id,
                'user_id' => $tecnico->id,
                'tipo' => 'correctivo',
                'descripcion' => 'Pantalla parpadea',
                'fecha' => '2026-06-28',
            ]);

        $response->assertRedirect(route('mantenimientos.index'))
            ->assertSessionHas('success');

        $mantenimiento = Mantenimiento::where('descripcion', 'Pantalla parpadea')->firstOrFail();
        
        // Assert notifications
        Notification::assertSentTo($tecnico, MantenimientoAsignado::class);
        Notification::assertSentTo($admin, MantenimientoRegistradoAdmin::class);

        // Check device state synchronized to con_falla because it is correctivo
        $this->assertSame('con_falla', $equipo->refresh()->estado);
    }

    public function test_admin_can_update_mantenimiento_and_upload_fotos(): void
    {
        Storage::fake('public');
        Notification::fake();

        $admin = $this->userWithRole('administrador');
        $tecnico = $this->userWithRole('tecnico');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Interno']);
        $comp = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'Memoria RAM',
        ]);
        $equipo = Equipo::create([
            'codigo' => 'AIP-304',
            'nombre' => 'PC Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);
        $equipo->componentes()->attach($comp->id, ['estado' => 'bueno']);

        $mantenimiento = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'preventivo',
            'descripcion' => 'Limpieza',
            'fecha' => '2026-06-28',
            'estado' => 'pendiente',
        ]);

        $fotoFile1 = UploadedFile::fake()->create('foto1.jpg', 15, 'image/jpeg');
        $fotoFile2 = UploadedFile::fake()->create('foto2.jpg', 20, 'image/jpeg');

        $response = $this->actingAs($admin)
            ->put(route('mantenimientos.update', $mantenimiento), [
                'diagnostico' => 'Sucia',
                'accion' => 'Limpieza de placa',
                'estado' => 'finalizado',
                'costo' => 50,
                'fotos' => [$fotoFile1, $fotoFile2],
                'componentes' => [
                    $comp->id => 'malo', // triggers HistorialFalla and FallaCriticaAlerta? Wait, if state transitions to finalizado, it goes to operativo, but let's test bad component behavior.
                ]
            ]);

        $response->assertRedirect(route('mantenimientos.show', $mantenimiento));

        $mantenimiento->refresh();
        $this->assertSame('finalizado', $mantenimiento->estado);
        $this->assertEquals(50, $mantenimiento->costo);
        $this->assertCount(2, $mantenimiento->fotos);

        // Assert file exists in storage
        foreach ($mantenimiento->fotos as $foto) {
            Storage::disk('public')->assertExists($foto->ruta);
        }

        // Since it's finalized, the team state should be operativo
        $this->assertSame('operativo', $equipo->refresh()->estado);

        // Component status should be updated to malo in pivot
        $this->assertDatabaseHas('equipo_componentes', [
            'equipo_id' => $equipo->id,
            'componente_id' => $comp->id,
            'estado' => 'malo',
        ]);

        // Falla critique should be logged since component is malo
        $this->assertDatabaseHas('historial_fallas', [
            'equipo_id' => $equipo->id,
            'componente_id' => $comp->id,
        ]);

        // Check finalized notification
        Notification::assertSentTo($admin, MantenimientoFinalizado::class);
    }

    public function test_tecnico_can_start_mantenimiento(): void
    {
        $tecnico = $this->userWithRole('tecnico');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-305',
            'nombre' => 'PC Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        $mantenimiento = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'correctivo',
            'descripcion' => 'Revisar',
            'fecha' => '2026-06-28',
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($tecnico)
            ->patch(route('mantenimientos.iniciar', $mantenimiento));

        $response->assertRedirect(route('mantenimientos.show', $mantenimiento))
            ->assertSessionHas('success');

        $this->assertSame('en_proceso', $mantenimiento->refresh()->estado);
    }

    public function test_tecnico_cannot_view_finished_mantenimiento(): void
    {
        $tecnico = $this->userWithRole('tecnico');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-306',
            'nombre' => 'PC Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        $mantenimiento = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $tecnico->id,
            'tipo' => 'correctivo',
            'descripcion' => 'Revisar',
            'fecha' => '2026-06-28',
            'estado' => 'finalizado',
        ]);

        // get show on finalizado redirect to index for technicians
        $this->actingAs($tecnico)
            ->get(route('mantenimientos.show', $mantenimiento))
            ->assertRedirect(route('mantenimientos.index'))
            ->assertSessionHas('error');
    }

    public function test_can_delete_evidence_photo(): void
    {
        Storage::fake('public');
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-307',
            'nombre' => 'PC Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        $mantenimiento = Mantenimiento::create([
            'equipo_id' => $equipo->id,
            'user_id' => $admin->id,
            'tipo' => 'correctivo',
            'descripcion' => 'Revisar',
            'fecha' => '2026-06-28',
            'estado' => 'pendiente',
        ]);

        $path = Storage::disk('public')->put('evidencias', UploadedFile::fake()->create('foto.jpg', 10));

        $foto = MantenimientoFoto::create([
            'mantenimiento_id' => $mantenimiento->id,
            'ruta' => $path,
            'nombre_original' => 'foto.jpg',
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($admin)
            ->delete(route('mantenimientos.fotos.eliminar', $foto));

        $response->assertRedirect();
        
        $this->assertDatabaseMissing('mantenimiento_fotos', [
            'id' => $foto->id,
        ]);
        Storage::disk('public')->assertMissing($path);
    }
}

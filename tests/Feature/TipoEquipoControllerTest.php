<?php

namespace Tests\Feature;

use App\Models\TipoEquipo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TipoEquipoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_tipos_equipos(): void
    {
        $tecnico = $this->userWithRole('tecnico');

        $this->actingAs($tecnico)
            ->get(route('tipos-equipos.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_tipos_equipos(): void
    {
        $admin = $this->userWithRole('administrador');
        TipoEquipo::create(['nombre' => 'Servidores']);
        TipoEquipo::create(['nombre' => 'Laptops']);

        $response = $this->actingAs($admin)
            ->get(route('tipos-equipos.index'));

        $response->assertOk()
            ->assertViewIs('tipos_equipos.index')
            ->assertSee('Servidores')
            ->assertSee('Laptops');
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = $this->userWithRole('administrador');

        $this->actingAs($admin)
            ->get(route('tipos-equipos.create'))
            ->assertOk()
            ->assertViewIs('tipos_equipos.create');
    }

    public function test_admin_can_store_tipo_equipo_with_image(): void
    {
        Storage::fake('public');
        $admin = $this->userWithRole('administrador');

        $file = UploadedFile::fake()->create('servidor.png', 10, 'image/png');

        $response = $this->actingAs($admin)
            ->post(route('tipos-equipos.store'), [
                'nombre' => 'Servidor Rack',
                'imagen' => $file,
            ]);

        $response->assertRedirect(route('tipos-equipos.index'))
            ->assertSessionHas('success');

        $tipo = TipoEquipo::where('nombre', 'Servidor Rack')->firstOrFail();
        $this->assertNotNull($tipo->imagen);

        // Verify stored file
        $relativePath = str_replace('/storage/', '', $tipo->imagen);
        Storage::disk('public')->assertExists($relativePath);
    }

    public function test_cannot_store_tipo_equipo_with_duplicate_name(): void
    {
        $admin = $this->userWithRole('administrador');
        TipoEquipo::create(['nombre' => 'Proyector']);

        $this->actingAs($admin)
            ->post(route('tipos-equipos.store'), [
                'nombre' => 'Proyector',
            ])
            ->assertSessionHasErrors('nombre');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'Switches']);

        $this->actingAs($admin)
            ->get(route('tipos-equipos.edit', $tipo))
            ->assertOk()
            ->assertViewIs('tipos_equipos.edit')
            ->assertSee('Switches');
    }

    public function test_admin_can_update_tipo_equipo_and_replace_image(): void
    {
        Storage::fake('public');
        $admin = $this->userWithRole('administrador');

        // Create with initial image
        $oldFile = UploadedFile::fake()->create('old.png', 10, 'image/png');
        $oldPath = $oldFile->store('tipos_equipos', 'public');

        $tipo = TipoEquipo::create([
            'nombre' => 'Switch Capa 2',
            'imagen' => '/storage/' . $oldPath,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        // Update with new name and new image
        $newFile = UploadedFile::fake()->create('new.png', 10, 'image/png');

        $response = $this->actingAs($admin)
            ->put(route('tipos-equipos.update', $tipo), [
                'nombre' => 'Switch Capa 3',
                'imagen' => $newFile,
            ]);

        $response->assertRedirect(route('tipos-equipos.index'))
            ->assertSessionHas('success');

        $tipo->refresh();
        $this->assertSame('Switch Capa 3', $tipo->nombre);

        $newPath = str_replace('/storage/', '', $tipo->imagen);
        Storage::disk('public')->assertExists($newPath);
        // The old file should have been deleted
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_admin_can_destroy_tipo_equipo_and_delete_image(): void
    {
        Storage::fake('public');
        $admin = $this->userWithRole('administrador');

        $file = UploadedFile::fake()->create('tipo.png', 10, 'image/png');
        $path = $file->store('tipos_equipos', 'public');

        $tipo = TipoEquipo::create([
            'nombre' => 'Access Point',
            'imagen' => '/storage/' . $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->actingAs($admin)
            ->delete(route('tipos-equipos.destroy', $tipo));

        $response->assertRedirect(route('tipos-equipos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('tipo_equipos', [
            'id' => $tipo->id,
        ]);
        Storage::disk('public')->assertMissing($path);
    }
}

<?php

namespace Tests\Feature;

use App\Models\CategoriaComponente;
use App\Models\Componente;
use App\Models\TipoEquipo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComponenteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_componentes(): void
    {
        $tecnico = $this->userWithRole('tecnico');

        $this->actingAs($tecnico)
            ->get(route('componentes.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_componentes(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Almacenamiento']);
        Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'SSD 1TB',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('componentes.index'));

        $response->assertOk()
            ->assertViewIs('componentes.index')
            ->assertSee('SSD 1TB');
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->get(route('componentes.create'));

        $response->assertOk()
            ->assertViewIs('componentes.create');
    }

    public function test_admin_can_store_componente_with_image(): void
    {
        Storage::fake('public');
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Almacenamiento']);

        $file = UploadedFile::fake()->create('ssd.png', 10, 'image/png');

        $response = $this->actingAs($admin)
            ->post(route('componentes.store'), [
                'tipo_id' => $tipo->id,
                'categoria_id' => $cat->id,
                'nombre' => 'SSD 2TB',
                'imagen' => $file,
            ]);

        $response->assertRedirect(route('componentes.index'))
            ->assertSessionHas('success');

        $componente = Componente::where('nombre', 'SSD 2TB')->firstOrFail();
        $this->assertNotNull($componente->imagen);

        $path = str_replace('/storage/', '', $componente->imagen);
        Storage::disk('public')->assertExists($path);
    }

    public function test_cannot_store_duplicate_component_name_for_same_type(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo1 = TipoEquipo::create(['nombre' => 'PC']);
        $tipo2 = TipoEquipo::create(['nombre' => 'Laptop']);
        $cat = CategoriaComponente::create(['nombre' => 'Almacenamiento']);

        Componente::create([
            'tipo_id' => $tipo1->id,
            'categoria_id' => $cat->id,
            'nombre' => 'SSD 1TB',
        ]);

        // Duplicate for same type => fail
        $this->actingAs($admin)
            ->post(route('componentes.store'), [
                'tipo_id' => $tipo1->id,
                'categoria_id' => $cat->id,
                'nombre' => 'SSD 1TB',
            ])
            ->assertSessionHasErrors('nombre');

        // Same name but different type => pass
        $this->actingAs($admin)
            ->post(route('componentes.store'), [
                'tipo_id' => $tipo2->id,
                'categoria_id' => $cat->id,
                'nombre' => 'SSD 1TB',
            ])
            ->assertRedirect(route('componentes.index'));
    }

    public function test_admin_can_view_edit_form(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Almacenamiento']);
        $componente = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'SSD 1TB',
        ]);

        $this->actingAs($admin)
            ->get(route('componentes.edit', $componente))
            ->assertOk()
            ->assertViewIs('componentes.edit')
            ->assertSee('SSD 1TB');
    }

    public function test_admin_can_update_componente(): void
    {
        Storage::fake('public');
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Almacenamiento']);

        $oldFile = UploadedFile::fake()->create('old.png', 10, 'image/png');
        $oldPath = $oldFile->store('componentes', 'public');

        $componente = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'SSD 1TB',
            'imagen' => '/storage/' . $oldPath,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->create('new.png', 10, 'image/png');

        $response = $this->actingAs($admin)
            ->put(route('componentes.update', $componente), [
                'tipo_id' => $tipo->id,
                'categoria_id' => $cat->id,
                'nombre' => 'SSD 1TB M.2',
                'imagen' => $newFile,
            ]);

        $response->assertRedirect(route('componentes.index'))
            ->assertSessionHas('success');

        $componente->refresh();
        $this->assertSame('SSD 1TB M.2', $componente->nombre);

        $newPath = str_replace('/storage/', '', $componente->imagen);
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_admin_can_show_componente(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Almacenamiento']);
        $componente = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'SSD 1TB',
        ]);

        $this->actingAs($admin)
            ->get(route('componentes.show', $componente))
            ->assertOk()
            ->assertViewIs('componentes.show')
            ->assertSee('SSD 1TB');
    }

    public function test_admin_can_destroy_componente(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Almacenamiento']);
        $componente = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'SSD 1TB',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('componentes.destroy', $componente));

        $response->assertRedirect(route('componentes.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('componentes', [
            'id' => $componente->id,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\CategoriaComponente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriaComponenteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_categorias(): void
    {
        $tecnico = $this->userWithRole('tecnico');

        $this->actingAs($tecnico)
            ->get(route('categorias.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_categorias(): void
    {
        $admin = $this->userWithRole('administrador');
        CategoriaComponente::create(['nombre' => 'Almacenamiento']);
        CategoriaComponente::create(['nombre' => 'Procesamiento']);

        $response = $this->actingAs($admin)
            ->get(route('categorias.index'));

        $response->assertOk()
            ->assertViewIs('categorias.index')
            ->assertSee('Almacenamiento')
            ->assertSee('Procesamiento');
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = $this->userWithRole('administrador');

        $this->actingAs($admin)
            ->get(route('categorias.create'))
            ->assertOk()
            ->assertViewIs('categorias.create');
    }

    public function test_admin_can_store_categoria(): void
    {
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->post(route('categorias.store'), [
                'nombre' => 'Memoria RAM',
            ]);

        $response->assertRedirect(route('categorias.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categorias_componentes', [
            'nombre' => 'Memoria RAM',
        ]);
    }

    public function test_cannot_store_categoria_without_name_or_duplicate(): void
    {
        $admin = $this->userWithRole('administrador');
        CategoriaComponente::create(['nombre' => 'Existente']);

        // Test required
        $this->actingAs($admin)
            ->post(route('categorias.store'), [
                'nombre' => '',
            ])
            ->assertSessionHasErrors('nombre');

        // Test unique
        $this->actingAs($admin)
            ->post(route('categorias.store'), [
                'nombre' => 'Existente',
            ])
            ->assertSessionHasErrors('nombre');
    }

    public function test_admin_can_view_edit_form(): void
    {
        $admin = $this->userWithRole('administrador');
        $categoria = CategoriaComponente::create(['nombre' => 'Placas']);

        $this->actingAs($admin)
            ->get(route('categorias.edit', $categoria))
            ->assertOk()
            ->assertViewIs('categorias.edit')
            ->assertSee('Placas');
    }

    public function test_admin_can_update_categoria(): void
    {
        $admin = $this->userWithRole('administrador');
        $categoria = CategoriaComponente::create(['nombre' => 'Placas']);

        $response = $this->actingAs($admin)
            ->put(route('categorias.update', $categoria), [
                'nombre' => 'Placas Madre',
            ]);

        $response->assertRedirect(route('categorias.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categorias_componentes', [
            'id' => $categoria->id,
            'nombre' => 'Placas Madre',
        ]);
    }

    public function test_admin_can_destroy_categoria(): void
    {
        $admin = $this->userWithRole('administrador');
        $categoria = CategoriaComponente::create(['nombre' => 'Eliminar']);

        $response = $this->actingAs($admin)
            ->delete(route('categorias.destroy', $categoria));

        $response->assertRedirect(route('categorias.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('categorias_componentes', [
            'id' => $categoria->id,
        ]);
    }
}

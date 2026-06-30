<?php

namespace Tests\Feature;

use App\Models\CategoriaComponente;
use App\Models\Componente;
use App\Models\Equipo;
use App\Models\TipoEquipo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_equipos(): void
    {
        $tecnico = $this->userWithRole('tecnico');

        $this->actingAs($tecnico)
            ->get(route('equipos.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_equipos_with_filters(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo1 = TipoEquipo::create(['nombre' => 'PC']);
        $tipo2 = TipoEquipo::create(['nombre' => 'Laptop']);

        Equipo::create([
            'codigo' => 'AIP-101',
            'nombre' => 'PC Especial',
            'tipo_id' => $tipo1->id,
            'estado' => 'operativo',
        ]);
        Equipo::create([
            'codigo' => 'AIP-102',
            'nombre' => 'Laptop Docente',
            'tipo_id' => $tipo2->id,
            'estado' => 'con_falla',
        ]);

        // Search test
        $this->actingAs($admin)
            ->get(route('equipos.index', ['search' => 'Especial']))
            ->assertOk()
            ->assertSee('PC Especial')
            ->assertDontSee('Laptop Docente');

        // State filter test
        $this->actingAs($admin)
            ->get(route('equipos.index', ['estado' => 'con_falla']))
            ->assertOk()
            ->assertSee('Laptop Docente')
            ->assertDontSee('PC Especial');

        // Type filter test
        $this->actingAs($admin)
            ->get(route('equipos.index', ['tipo_id' => $tipo1->id]))
            ->assertOk()
            ->assertSee('PC Especial')
            ->assertDontSee('Laptop Docente');
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = $this->userWithRole('administrador');

        $this->actingAs($admin)
            ->get(route('equipos.create'))
            ->assertOk()
            ->assertViewIs('equipos.create');
    }

    public function test_admin_can_store_equipo_with_auto_component_binding(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Interno']);

        // Component linked to type
        $comp1 = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'Memoria RAM',
        ]);
        $comp2 = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'Disco Duro',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('equipos.store'), [
                'codigo' => 'AIP-005',
                'nombre' => 'Nueva PC Laboratorio',
                'tipo_id' => $tipo->id,
                'marca' => 'HP',
                'modelo' => 'ProDesk',
                'estado' => 'operativo',
                'frecuencia_mantenimiento' => 6,
            ]);

        $response->assertRedirect(route('equipos.index'))
            ->assertSessionHas('success');

        $equipo = Equipo::where('codigo', 'AIP-005')->firstOrFail();

        // Components should be automatically attached
        $this->assertDatabaseHas('equipo_componentes', [
            'equipo_id' => $equipo->id,
            'componente_id' => $comp1->id,
            'estado' => 'bueno',
        ]);
        $this->assertDatabaseHas('equipo_componentes', [
            'equipo_id' => $equipo->id,
            'componente_id' => $comp2->id,
            'estado' => 'bueno',
        ]);
    }

    public function test_admin_can_update_equipo_relinking_components_on_type_change(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipoOld = TipoEquipo::create(['nombre' => 'PC']);
        $tipoNew = TipoEquipo::create(['nombre' => 'Laptop']);
        $cat = CategoriaComponente::create(['nombre' => 'Interno']);

        $compOld = Componente::create([
            'tipo_id' => $tipoOld->id,
            'categoria_id' => $cat->id,
            'nombre' => 'Disco de Escritorio',
        ]);
        $compNew = Componente::create([
            'tipo_id' => $tipoNew->id,
            'categoria_id' => $cat->id,
            'nombre' => 'SSD Laptop',
        ]);

        $equipo = Equipo::create([
            'codigo' => 'AIP-006',
            'nombre' => 'Estacion Trabajo',
            'tipo_id' => $tipoOld->id,
            'estado' => 'operativo',
        ]);
        $equipo->componentes()->attach($compOld->id, ['estado' => 'bueno']);

        // Update changing the type
        $response = $this->actingAs($admin)
            ->put(route('equipos.update', $equipo), [
                'codigo' => 'AIP-006',
                'nombre' => 'Estacion Trabajo Actualizada',
                'tipo_id' => $tipoNew->id,
                'estado' => 'operativo',
                'frecuencia_mantenimiento' => 6,
            ]);

        $response->assertRedirect(route('equipos.index'))
            ->assertSessionHas('success');

        $equipo->refresh();
        $this->assertSame($tipoNew->id, $equipo->tipo_id);

        // Old components detached, new attached
        $this->assertDatabaseMissing('equipo_componentes', [
            'equipo_id' => $equipo->id,
            'componente_id' => $compOld->id,
        ]);
        $this->assertDatabaseHas('equipo_componentes', [
            'equipo_id' => $equipo->id,
            'componente_id' => $compNew->id,
            'estado' => 'bueno',
        ]);
    }

    public function test_admin_can_destroy_equipo(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-007',
            'nombre' => 'Eliminar Equipo',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('equipos.destroy', $equipo));

        $response->assertRedirect(route('equipos.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('equipos', [
            'id' => $equipo->id,
        ]);
    }

    public function test_admin_can_add_custom_component_to_equipo(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $equipo = Equipo::create([
            'codigo' => 'AIP-008',
            'nombre' => 'Equipo Especial',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('equipos.componentes.agregar', $equipo), [
                'nombre' => 'Tarjeta Grafica',
            ]);

        $response->assertRedirect();
        
        $componente = Componente::where('nombre', 'Tarjeta grafica')->firstOrFail();

        $this->assertDatabaseHas('equipo_componentes', [
            'equipo_id' => $equipo->id,
            'componente_id' => $componente->id,
            'estado' => 'bueno',
        ]);
    }

    public function test_admin_cannot_add_duplicate_component_to_equipo(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Interno']);
        $comp = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'Teclado',
        ]);
        $equipo = Equipo::create([
            'codigo' => 'AIP-009',
            'nombre' => 'Equipo Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);
        $equipo->componentes()->attach($comp->id, ['estado' => 'bueno']);

        $response = $this->actingAs($admin)
            ->post(route('equipos.componentes.agregar', $equipo), [
                'nombre' => 'Teclado',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_admin_can_remove_component_from_equipo(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Interno']);
        $comp = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'Raton',
        ]);
        $equipo = Equipo::create([
            'codigo' => 'AIP-010',
            'nombre' => 'Equipo Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);
        $equipo->componentes()->attach($comp->id, ['estado' => 'bueno']);

        $response = $this->actingAs($admin)
            ->delete(route('equipos.componentes.quitar', [$equipo, $comp]));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('equipo_componentes', [
            'equipo_id' => $equipo->id,
            'componente_id' => $comp->id,
        ]);
    }

    public function test_admin_can_change_component_pivot_state(): void
    {
        $admin = $this->userWithRole('administrador');
        $tipo = TipoEquipo::create(['nombre' => 'PC']);
        $cat = CategoriaComponente::create(['nombre' => 'Interno']);
        $comp = Componente::create([
            'tipo_id' => $tipo->id,
            'categoria_id' => $cat->id,
            'nombre' => 'Pantalla',
        ]);
        $equipo = Equipo::create([
            'codigo' => 'AIP-011',
            'nombre' => 'Equipo Test',
            'tipo_id' => $tipo->id,
            'estado' => 'operativo',
        ]);
        $equipo->componentes()->attach($comp->id, ['estado' => 'bueno']);

        $response = $this->actingAs($admin)
            ->put(route('equipos.componentes.estado', [$equipo, $comp]), [
                'estado' => 'regular',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('equipo_componentes', [
            'equipo_id' => $equipo->id,
            'componente_id' => $comp->id,
            'estado' => 'regular',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Configuracion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConfiguracionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_configuracion(): void
    {
        $tecnico = $this->userWithRole('tecnico');

        $this->actingAs($tecnico)
            ->get(route('configuracion.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_configuracion_page(): void
    {
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->get(route('configuracion.index'));

        $response->assertOk()
            ->assertViewIs('configuracion.index');
    }

    public function test_admin_can_update_configuracion_and_upload_logo(): void
    {
        Storage::fake('public');
        $admin = $this->userWithRole('administrador');

        $logoFile = UploadedFile::fake()->create('logo.png', 10, 'image/png');

        $response = $this->actingAs($admin)
            ->put(route('configuracion.update'), [
                'nombre_institucion' => 'AIP Central',
                'director_nombre' => 'Juan Perez',
                'ruc' => '20123456789',
                'direccion' => 'Av. Siempre Viva 742',
                'telefono' => '987654321',
                'email' => 'aip@central.edu',
                'logo' => $logoFile,
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $config = Configuracion::firstOrFail();
        $this->assertSame('AIP Central', $config->nombre_institucion);
        $this->assertSame('Juan Perez', $config->director_nombre);
        $this->assertSame('20123456789', $config->ruc);
        $this->assertSame('987654321', $config->telefono);
        $this->assertSame('aip@central.edu', $config->email);
        
        $this->assertNotNull($config->logo_path);
        Storage::disk('public')->assertExists($config->logo_path);
    }

    public function test_admin_can_replace_configuracion_logo(): void
    {
        Storage::fake('public');
        $admin = $this->userWithRole('administrador');

        $oldLogoPath = Storage::disk('public')->put('logos', UploadedFile::fake()->create('old.png', 5));
        
        $config = Configuracion::create([
            'nombre_institucion' => 'AIP Vieja',
            'logo_path' => $oldLogoPath,
        ]);

        Storage::disk('public')->assertExists($oldLogoPath);

        $newLogoFile = UploadedFile::fake()->create('new.png', 10, 'image/png');

        $response = $this->actingAs($admin)
            ->put(route('configuracion.update'), [
                'nombre_institucion' => 'AIP Nueva',
                'logo' => $newLogoFile,
            ]);

        $response->assertRedirect();
        
        $config->refresh();
        $this->assertSame('AIP Nueva', $config->nombre_institucion);
        Storage::disk('public')->assertExists($config->logo_path);
        
        // Old logo deleted from disk
        Storage::disk('public')->assertMissing($oldLogoPath);
    }
}

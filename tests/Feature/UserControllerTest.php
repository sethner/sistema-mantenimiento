<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure standard directories exist for photo upload
        if (!file_exists(public_path('img'))) {
            mkdir(public_path('img'), 0777, true);
        }
    }

    protected function tearDown(): void
    {
        // Clean up any photo files uploaded to public/img during tests
        $files = glob(public_path('img/*'));
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore' && basename($file) !== '.gitkeep') {
                unlink($file);
            }
        }
        parent::tearDown();
    }

    public function test_non_admin_cannot_access_users_management(): void
    {
        $tecnico = $this->userWithRole('tecnico');

        $this->actingAs($tecnico)
            ->get(route('usuarios.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_users_with_search_and_role_filters(): void
    {
        $admin = $this->userWithRole('administrador');
        $admin->update(['name' => 'Alfredo Admin', 'email' => 'admin@test.com']);

        $tecnico = $this->userWithRole('tecnico');
        $tecnico->update(['name' => 'Tomas Tecnico', 'email' => 'tecnico@test.com']);

        $supervisor = $this->userWithRole('supervisor');
        $supervisor->update(['name' => 'Sofia Supervisor', 'email' => 'supervisor@test.com']);

        // Search test
        $this->actingAs($admin)
            ->get(route('usuarios.index', ['search' => 'Tomas']))
            ->assertOk()
            ->assertSee('Tomas Tecnico')
            ->assertDontSee('Sofia Supervisor');

        // Role filter test
        $this->actingAs($admin)
            ->get(route('usuarios.index', ['rol' => 'tecnico']))
            ->assertOk()
            ->assertSee('Tomas Tecnico')
            ->assertDontSee('Sofia Supervisor');
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->get(route('usuarios.create'));

        $response->assertOk()
            ->assertViewIs('usuarios.create');
    }

    public function test_admin_can_store_user_with_avatar(): void
    {
        $admin = $this->userWithRole('administrador');
        $roleTecnico = Role::firstOrCreate(['nombre' => 'tecnico']);

        $file = UploadedFile::fake()->create('avatar.png', 10, 'image/png');

        $response = $this->actingAs($admin)
            ->post(route('usuarios.store'), [
                'name' => 'Nuevo Tecnico',
                'email' => 'nuevo@tecnico.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role_id' => $roleTecnico->id,
                'foto' => $file,
            ]);

        $response->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('success');

        $user = User::where('email', 'nuevo@tecnico.com')->firstOrFail();
        $this->assertSame('Nuevo Tecnico', $user->name);
        $this->assertTrue(Hash::check('Password123!', $user->password));
        $this->assertTrue($user->hasRole('tecnico'));
        
        $this->assertNotNull($user->foto);
        $this->assertFileExists(public_path($user->foto));
    }

    public function test_admin_can_update_user(): void
    {
        sleep(1);
        $admin = $this->userWithRole('administrador');
        $user = $this->userWithRole('tecnico');
        $roleSupervisor = Role::firstOrCreate(['nombre' => 'supervisor']);

        $file = UploadedFile::fake()->create('avatar_new.png', 10, 'image/png');

        $response = $this->actingAs($admin)
            ->put(route('usuarios.update', $user), [
                'name' => 'Tecnico Actualizado',
                'email' => 'actualizado@tecnico.com',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
                'role_id' => $roleSupervisor->id,
                'foto' => $file,
            ]);

        $response->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Tecnico Actualizado', $user->name);
        $this->assertSame('actualizado@tecnico.com', $user->email);
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));
        $this->assertTrue($user->hasRole('supervisor'));
        
        $this->assertNotNull($user->foto);
        $this->assertFileExists(public_path($user->foto));
    }

    public function test_admin_cannot_delete_administrator_user(): void
    {
        $admin = $this->userWithRole('administrador');
        $otherAdmin = $this->userWithRole('administrador');

        $response = $this->actingAs($admin)
            ->delete(route('usuarios.destroy', $otherAdmin));

        $response->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    }

    public function test_admin_can_delete_technician_user(): void
    {
        $admin = $this->userWithRole('administrador');
        $tecnico = $this->userWithRole('tecnico');

        // Create dummy photo to verify deletion
        $filePath = public_path('img/test_avatar.png');
        file_put_contents($filePath, 'dummy data');
        $tecnico->update(['foto' => 'img/test_avatar.png']);

        $this->assertFileExists($filePath);

        $response = $this->actingAs($admin)
            ->delete(route('usuarios.destroy', $tecnico));

        $response->assertRedirect(route('usuarios.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $tecnico->id]);
        $this->assertFileDoesNotExist($filePath);
    }

    public function test_admin_can_show_user_details(): void
    {
        $admin = $this->userWithRole('administrador');
        $tecnico = $this->userWithRole('tecnico');

        $this->actingAs($admin)
            ->get(route('usuarios.show', $tecnico))
            ->assertOk()
            ->assertViewIs('usuarios.show');
    }
}

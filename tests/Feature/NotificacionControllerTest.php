<?php

namespace Tests\Feature;

use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificacionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_notifications_and_they_are_marked_read(): void
    {
        $user = $this->userWithRole('tecnico');

        // Create notification
        $notif = Notificacion::create([
            'user_id' => $user->id,
            'tipo' => 'asignacion',
            'titulo' => 'Nueva orden',
            'mensaje' => 'Tienes una orden de mantenimiento asignada',
            'enlace' => '/mantenimientos',
            'leida' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('notificaciones.index'));

        $response->assertOk()
            ->assertViewIs('notificaciones.index')
            ->assertSee('Tienes una orden de mantenimiento asignada');

        // Automatic mark as read verified
        $this->assertTrue($notif->refresh()->leida);
    }

    public function test_user_can_mark_single_notification_as_read_via_ajax(): void
    {
        $user = $this->userWithRole('tecnico');
        $notif = Notificacion::create([
            'user_id' => $user->id,
            'tipo' => 'asignacion',
            'titulo' => 'Nueva orden',
            'mensaje' => 'Mensaje',
            'enlace' => '/mantenimientos',
            'leida' => false,
        ]);

        $response = $this->actingAs($user)
            ->post(route('notificaciones.leida', $notif));

        $response->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertTrue($notif->refresh()->leida);
    }

    public function test_user_cannot_mark_other_user_notification_as_read(): void
    {
        $user = $this->userWithRole('tecnico');
        $otherUser = $this->userWithRole('tecnico');
        $notif = Notificacion::create([
            'user_id' => $otherUser->id,
            'tipo' => 'asignacion',
            'titulo' => 'Nueva orden',
            'mensaje' => 'Mensaje',
            'enlace' => '/mantenimientos',
            'leida' => false,
        ]);

        $this->actingAs($user)
            ->post(route('notificaciones.leida', $notif))
            ->assertForbidden();
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = $this->userWithRole('tecnico');
        $n1 = Notificacion::create([
            'user_id' => $user->id,
            'tipo' => 'asignacion',
            'titulo' => 'N1',
            'mensaje' => 'Mensaje',
            'enlace' => '/mantenimientos',
            'leida' => false,
        ]);
        $n2 = Notificacion::create([
            'user_id' => $user->id,
            'tipo' => 'asignacion',
            'titulo' => 'N2',
            'mensaje' => 'Mensaje',
            'enlace' => '/mantenimientos',
            'leida' => false,
        ]);

        $response = $this->actingAs($user)
            ->post(route('notificaciones.marcar-todas'));

        $response->assertRedirect();
        
        $this->assertTrue($n1->refresh()->leida);
        $this->assertTrue($n2->refresh()->leida);
    }

    public function test_user_can_get_unread_notification_count(): void
    {
        $user = $this->userWithRole('tecnico');
        Notificacion::create([
            'user_id' => $user->id,
            'tipo' => 'asignacion',
            'titulo' => 'N1',
            'mensaje' => 'Mensaje',
            'enlace' => '/mantenimientos',
            'leida' => false,
        ]);
        Notificacion::create([
            'user_id' => $user->id,
            'tipo' => 'asignacion',
            'titulo' => 'N2',
            'mensaje' => 'Mensaje',
            'enlace' => '/mantenimientos',
            'leida' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('notificaciones.conteo'));

        $response->assertOk()
            ->assertJson(['count' => 1]);
    }

    public function test_user_can_clear_all_notifications(): void
    {
        $user = $this->userWithRole('tecnico');
        Notificacion::create([
            'user_id' => $user->id,
            'tipo' => 'asignacion',
            'titulo' => 'N1',
            'mensaje' => 'Mensaje',
            'enlace' => '/mantenimientos',
            'leida' => false,
        ]);

        $response = $this->actingAs($user)
            ->post(route('notificaciones.limpiar'));

        $response->assertRedirect();
        $this->assertEquals(0, Notificacion::count());
    }

    public function test_user_can_get_recent_unread_notifications(): void
    {
        $user = $this->userWithRole('tecnico');
        
        // 6 unread notifications with distinct timestamps to ensure ordering
        for ($i = 1; $i <= 6; $i++) {
            $notif = new Notificacion([
                'user_id' => $user->id,
                'tipo' => 'asignacion',
                'titulo' => "N{$i}",
                'mensaje' => "Mensaje {$i}",
                'enlace' => '/mantenimientos',
                'leida' => false,
            ]);
            $notif->created_at = now()->addSeconds($i);
            $notif->save();
        }

        $response = $this->actingAs($user)
            ->get(route('notificaciones.recientes'));

        $response->assertOk();
        $data = $response->json();
        
        // Should only return 5 most recent
        $this->assertCount(5, $data);
        $this->assertEquals('N6', $data[0]['titulo']);
    }
}

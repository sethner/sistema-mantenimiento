<?php

namespace App\Notifications;

use App\Models\Equipo;
use App\Notifications\Channels\DatabaseCustomChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FallaCriticaAlerta extends Notification implements ShouldQueue
{
    use Queueable;

    protected $equipo;

    /**
     * Create a new notification instance.
     */
    public function __construct(Equipo $equipo)
    {
        $this->equipo = $equipo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('ALERTA: Falla Crítica Detectada - ' . $this->equipo->nombre)
            ->markdown('emails.falla_critica', [
                'equipo' => $this->equipo,
                'notifiable' => $notifiable
            ]);
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'    => 'falla_critica',
            'titulo'  => 'Equipo con falla crítica',
            'mensaje' => "El equipo {$this->equipo->nombre} ha registrado una nueva falla.",
            'enlace'  => route('equipos.show', $this->equipo->id),
            'leida'   => false,
        ];
    }
}

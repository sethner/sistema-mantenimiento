<?php

namespace App\Notifications;

use App\Models\Mantenimiento;
use App\Notifications\Channels\DatabaseCustomChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MantenimientoAsignado extends Notification implements ShouldQueue
{
    use Queueable;

    protected $mantenimiento;

    /**
     * Create a new notification instance.
     */
    public function __construct(Mantenimiento $mantenimiento)
    {
        $this->mantenimiento = $mantenimiento;
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
            ->subject('Nuevo Mantenimiento Asignado - ' . $this->mantenimiento->equipo->nombre)
            ->markdown('emails.mantenimiento_asignado', [
                'mantenimiento' => $this->mantenimiento,
                'notifiable' => $notifiable
            ]);
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo'    => 'asignacion',
            'titulo'  => 'Nuevo mantenimiento asignado',
            'mensaje' => "Se te asignó un mantenimiento en el equipo: {$this->mantenimiento->equipo->nombre}.",
            'enlace'  => route('mantenimientos.show', $this->mantenimiento->id),
            'leida'   => false,
        ];
    }
}

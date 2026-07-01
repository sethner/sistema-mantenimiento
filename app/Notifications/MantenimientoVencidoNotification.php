<?php

namespace App\Notifications;

use App\Models\Mantenimiento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MantenimientoVencidoNotification extends Notification implements ShouldQueue
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
            ->subject('ALERTA: Mantenimiento VENCIDO - ' . $this->mantenimiento->equipo->nombre)
            ->markdown('emails.mantenimiento_vencido', [
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
            'tipo'    => 'alerta_vencido',
            'titulo'  => 'Mantenimiento VENCIDO',
            'mensaje' => "El mantenimiento programado para el equipo {$this->mantenimiento->equipo->nombre} venció el {$this->mantenimiento->fecha->format('d/m/Y')}.",
            'enlace'  => $notifiable->hasRole('administrador') ? null : route('mantenimientos.show', $this->mantenimiento->id),
            'leida'   => false,
        ];
    }
}

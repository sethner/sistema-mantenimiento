<?php

namespace App\Notifications;

use App\Models\Mantenimiento;
use App\Notifications\Channels\DatabaseCustomChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MantenimientoRegistradoAdmin extends Notification implements ShouldQueue
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
            ->subject('Registro de Mantenimiento - ' . $this->mantenimiento->equipo->nombre)
            ->markdown('emails.mantenimiento_registrado_admin', [
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
            'tipo'    => 'registro_mantenimiento',
            'titulo'  => 'Nuevo mantenimiento registrado',
            'mensaje' => "Se registró un mantenimiento para {$this->mantenimiento->equipo->nombre} (Técnico: {$this->mantenimiento->usuario->name}).",
            'enlace'  => route('mantenimientos.show', $this->mantenimiento->id),
            'leida'   => false,
        ];
    }
}

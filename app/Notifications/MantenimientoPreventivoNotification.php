<?php

namespace App\Notifications;

use App\Models\Equipo;
use App\Notifications\Channels\DatabaseCustomChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MantenimientoPreventivoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $equipo;
    protected $esVencido;

    /**
     * Create a new notification instance.
     */
    public function __construct(Equipo $equipo)
    {
        $this->equipo = $equipo;
        $this->esVencido = $equipo->proximo_mantenimiento->isBefore(today());
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
        $subject = $this->esVencido 
            ? 'ALERTA: Mantenimiento VENCIDO - ' . $this->equipo->nombre 
            : 'Recordatorio: Mantenimiento Próximo - ' . $this->equipo->nombre;

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.mantenimiento_preventivo', [
                'equipo' => $this->equipo,
                'esVencido' => $this->esVencido,
                'notifiable' => $notifiable
            ]);
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $titulo = $this->esVencido ? 'Mantenimiento VENCIDO' : 'Mantenimiento Preventivo Próximo';
        $mensaje = $this->esVencido 
            ? "El mantenimiento de {$this->equipo->nombre} venció el {$this->equipo->proximo_mantenimiento->format('d/m/Y')}."
            : "El equipo {$this->equipo->nombre} requiere mantenimiento el {$this->equipo->proximo_mantenimiento->format('d/m/Y')}.";

        return [
            'tipo'    => 'alerta_vencimiento',
            'titulo'  => $titulo,
            'mensaje' => $mensaje,
            'enlace'  => route('equipos.show', $this->equipo->id),
            'leida'   => false,
        ];
    }
}

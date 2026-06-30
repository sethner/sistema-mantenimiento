<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class DatabaseCustomChannel
{
    /**
     * Enviar la notificación dada.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toCustomDatabase($notifiable);

        \App\Models\Notificacion::create([
            'user_id' => $notifiable->id,
            'tipo'    => $data['tipo'],
            'titulo'  => $data['titulo'],
            'mensaje' => $data['mensaje'],
            'enlace'  => $data['enlace'],
        ]);
    }
}

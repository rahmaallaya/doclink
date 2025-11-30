<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminMessageReceived extends Notification
{
    use Queueable;

    public $ticket;

    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['database']; // Pas de broadcast ici, on gère manuellement
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id'            => $notifiable->id,
            'receiver_id'        => $notifiable->id,
            'type'               => 'support_ticket',
            'related_id'         => $this->ticket->id,
            'message'            => "Nouveau ticket de <strong>{$this->ticket->user->name}</strong> : {$this->ticket->subject}",
            'read'               => 0,
        ];
    }
}
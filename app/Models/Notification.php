<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'receiver_id', 'receiver_specialty',
        'type', 'related_id', 'message', 'read'
    ];

    protected $casts = [
        'read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'related_id');
    }

    // Scope principal : toutes les notifs pour un utilisateur
    public function scopeForUser($query, $user)
{
    return $query->where(function ($q) use ($user) {
        $q->where('receiver_id', $user->id)
          ->orWhere(function ($q2) use ($user) {
              $q2->whereNull('receiver_id')
                 ->where('receiver_specialty', $user->role === 'admin' ? 'admin' : $user->specialty)
                 ->where('user_id', '!=', $user->id);
          });
    });
}
    public function broadcastOn()
    {
        $channels = [];

        if ($this->receiver_id) {
            $channels[] = new PrivateChannel('user.' . $this->receiver_id);
        }

        if ($this->receiver_specialty) {
            $channels[] = new PrivateChannel('specialty.' . $this->receiver_specialty);
        }

        return $channels;
    }

    public function broadcastWith()
    {
        return [
            'id'      => $this->id,
            'message' => Str::limit(strip_tags($this->message), 70),
            'time'    => $this->created_at->diffForHumans(),
            'url'     => route('notifications.show', $this->id),
        ];
    }
    // Dans scopeForUser() de Notification.php
public function notifyNewPrivateMessage($privateMessage)
    {
        $sender = $privateMessage->sender;

        $this->create(
            senderId: $privateMessage->sender_id,
            receiverId: $privateMessage->receiver_id,
            receiverSpecialty: null,
            type: 'new_private_message',
            relatedId: $privateMessage->id,
            message: "Nouveau message privé de <strong>{$sender->name}</strong>" .
                     ($privateMessage->subject ? " : <em>{$privateMessage->subject}</em>" : "")
        );
    }
}
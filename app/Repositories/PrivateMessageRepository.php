<?php

namespace App\Repositories;

use App\Models\PrivateMessage;
use Illuminate\Support\Collection;
use App\Repositories\Interfaces\PrivateMessageRepositoryInterface;

class PrivateMessageRepository implements PrivateMessageRepositoryInterface
{
    public function getConversation(int $userId1, int $userId2): Collection
    {
        return PrivateMessage::betweenUsers($userId1, $userId2)
            ->orderBy('created_at', 'asc')
            ->with(['sender', 'receiver'])
            ->get();
    }

    public function sendMessage(array $data): PrivateMessage
    {
        return PrivateMessage::create($data);
    }

    public function markAsRead(int $messageId): bool
    {
        $message = PrivateMessage::findOrFail($messageId);
        return $message->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    public function getUnreadCount(int $userId): int
    {
        return PrivateMessage::where('receiver_id', $userId)
            ->unread()
            ->count();
    }

    public function getConversationsList(int $userId): Collection
    {
        // Récupérer toutes les conversations (derniers messages)
        return PrivateMessage::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->with(['sender', 'receiver'])
            ->get()
            ->unique(function ($message) use ($userId) {
                // Grouper par l'autre utilisateur
                return $message->sender_id === $userId 
                    ? $message->receiver_id 
                    : $message->sender_id;
            });
    }
    // app/Repositories/PrivateMessageRepository.php

public function markConversationAsRead(int $currentUserId, int $otherUserId): int
{
    return PrivateMessage::where('receiver_id', $currentUserId)
                         ->where('sender_id', $otherUserId)
                         ->where('read', false)
                         ->update([
                             'read' => true,
                             'read_at' => now()
                         ]);
}
}
<?php

namespace App\Repositories\Interfaces;

use App\Models\PrivateMessage;
use Illuminate\Support\Collection;

interface PrivateMessageRepositoryInterface
{
    public function getConversation(int $userId1, int $userId2): Collection;
    public function sendMessage(array $data): PrivateMessage;
    public function markAsRead(int $messageId): bool;
    public function getUnreadCount(int $userId): int;
    public function getConversationsList(int $userId): Collection;

    // AJOUTÉE ICI
    public function markConversationAsRead(int $currentUserId, int $otherUserId): int;
}
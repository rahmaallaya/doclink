<?php

namespace App\Services;

use App\Repositories\Interfaces\PrivateMessageRepositoryInterface;
use App\Models\Notification;

class PrivateMessageService
{
    protected $repository;
    protected $notificationService;

    public function __construct(
        PrivateMessageRepositoryInterface $repository,
        NotificationService $notificationService
    ) {
        $this->repository = $repository;
        $this->notificationService = $notificationService;
    }

    public function getConversation(int $userId1, int $userId2)
    {
        return $this->repository->getConversation($userId1, $userId2);
    }

    public function sendMessage(int $senderId, int $receiverId, ?string $subject, string $message)
    {
        $privateMessage = $this->repository->sendMessage([
            'sender_id'   => $senderId,
            'receiver_id' => $receiverId,
            'subject'     => $subject,
            'message'     => $message,
            'read'        => false,
        ]);

        // Notification au destinataire
        $this->notificationService->notifyNewPrivateMessage($privateMessage);

        return $privateMessage;
    }

    public function markAsRead(int $messageId)
    {
        return $this->repository->markAsRead($messageId);
    }

    // AJOUT DE CETTE MÉTHODE (c'était ce qui manquait !)
    public function markConversationAsRead(int $currentUserId, int $otherUserId)
    {
        return $this->repository->markConversationAsRead($currentUserId, $otherUserId);
    }

    public function getUnreadCount(int $userId)
    {
        return $this->repository->getUnreadCount($userId);
    }

    public function getConversationsList(int $userId)
    {
        return $this->repository->getConversationsList($userId);
    }
}
<?php

namespace App\Repositories\Interfaces;

use App\Models\AdminMessage;

interface AdminMessageRepositoryInterface
{
    public function createMessage(array $data): AdminMessage;
    public function getMessagesByUser(int $userId);
    public function getAllMessages();
    public function respondToMessage(int $messageId, string $response): AdminMessage;
    public function resolveMessage(int $messageId): AdminMessage;
}
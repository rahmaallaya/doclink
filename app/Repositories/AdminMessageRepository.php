<?php

namespace App\Repositories;

use App\Models\AdminMessage;
use App\Repositories\Interfaces\AdminMessageRepositoryInterface;

class AdminMessageRepository implements AdminMessageRepositoryInterface
{
    public function createMessage(array $data)
    {
        return AdminMessage::create($data);
    }

    public function getMessagesByUser(int $userId)
    {
        return AdminMessage::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAllMessages()
    {
        return AdminMessage::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function respondToMessage(int $messageId, string $response)
    {
        $message = AdminMessage::findOrFail($messageId);
        $message->admin_response = $response;
        $message->save();

        return $message;
    }

    public function resolveMessage(int $messageId)
    {
        $message = AdminMessage::findOrFail($messageId);
        $message->status = 'resolved';
        $message->resolved_at = now();
        $message->save();

        return $message;
    }
}
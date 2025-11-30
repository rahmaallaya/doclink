<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;

class NotificationService
{
    public function create($senderId, $receiverId, $receiverSpecialty, $type, $relatedId, $message)
    {
        return Notification::create([
            'user_id'            => $senderId,
            'receiver_id'        => $receiverId,
            'receiver_specialty' => $receiverSpecialty,
            'type'               => $type,
            'related_id'         => $relatedId,
            'message'            => $message,
            'read'               => false,
        ]);
        broadcast(new \App\Events\NewNotification($notification)); // ← AJOUTE ÇA

return $notification;
    }

    // ANNULATION RDV – 100% CORRIGÉ
    public function notifyAppointmentCancelled(Appointment $appointment, string $cancelledBy = 'patient')
    {
        $patient = $appointment->user;
        $doctor  = $appointment->doctor;
        $date    = Carbon::parse($appointment->appointment_time)->format('d/m/Y à H:i');

        if ($cancelledBy === 'patient') {
            // Patient annule → médecin reçoit
            $this->create(
                $patient->id,
                $doctor->id,
                null,
                'appointment_cancelled_by_patient',
                $appointment->id,
                "Le patient {$patient->name} a annulé le rendez-vous du {$date}"
            );
        } else {
            // Médecin annule → patient reçoit
            $this->create(
                $doctor->id,
                $patient->id,
                null,
                'appointment_cancelled_by_doctor',
                $appointment->id,
                "Le Dr {$doctor->name} a annulé votre rendez-vous du {$date}"
            );
        }
    }

    public function notifyAppointmentBooked(Appointment $appointment)
    {
        $date = Carbon::parse($appointment->appointment_time)->format('d/m/Y à H:i');

        $this->create($appointment->user_id, $appointment->user_id, null, 'appointment_booked', $appointment->id,
            "Rendez-vous confirmé avec le Dr {$appointment->doctor->name} le {$date}");

        $this->create($appointment->user_id, $appointment->doctor_id, null, 'appointment_booked', $appointment->id,
            "Nouveau rendez-vous avec {$appointment->user->name} le {$date}");
    }

    // Rappel 24h avant RDV
    public function sendAppointmentReminder(Appointment $appointment)
    {
        $time = Carbon::parse($appointment->appointment_time);
        if ($time->diffInHours(now()) <= 24 && $time->isFuture()) {
            $exists = Notification::where('type', 'appointment_reminder')
                ->where('related_id', $appointment->id)->exists();
            if (!$exists) {
                $this->create($appointment->user_id, $appointment->user_id, null, 'appointment_reminder', $appointment->id,
                    "Rappel : rendez-vous demain à {$time->format('H:i')} avec le Dr {$appointment->doctor->name}");
            }
        }
    }

    // Méthodes utilitaires
    public function getForUser(User $user, $perPage = 15)
    {
        return Notification::forUser($user)
            ->with(['sender', 'appointment'])
            ->latest()
            ->paginate($perPage);
    }

    public function getUnreadCount(User $user)
    {
        return Notification::forUser($user)->where('read', false)->count();
    }

    public function getRecent(User $user, $limit = 10)
    {
        return Notification::forUser($user)->latest()->limit($limit)->get();
    }

    public function markAsRead($id, User $user)
    {
        $notif = Notification::forUser($user)->findOrFail($id);
        $notif->update(['read' => true]);
        return $notif;
    }

    public function markAllAsRead(User $user)
    {
        Notification::forUser($user)->where('read', false)->update(['read' => true]);
    }
    // app/Services/NotificationService.php

/**
 * Notifie tous les médecins d'une spécialité quand une nouvelle question est posée
 */
public function notifyNewQuestionInSpecialty($question)
{
    // Récupère la spécialité via la catégorie
    $specialty = $question->category->specialty ?? null;

    if (!$specialty) return;

    $this->create(
        senderId: $question->patient_id,
        receiverId: null,                    // null = diffusion par spécialité
        receiverSpecialty: $specialty,       // ← c’est ça qui cible les médecins de cette spécialité
        type: 'new_forum_question',
        relatedId: $question->id,
        message: "Nouvelle question en <strong>{$specialty}</strong> : « {$question->title} »"
    );
}

/**
 * Notifie le patient quand un médecin répond à sa question
 */
public function notifyQuestionAnswered($question, $answer)
{
    $this->create(
        senderId: $answer->doctor_id,
        receiverId: $question->patient_id,
        receiverSpecialty: null,
        type: 'forum_answer',
        relatedId: $question->id,
        message: "Un médecin a répondu à votre question : « {$question->title} »"
    );
}
/**
 * Notifie le destinataire quand il reçoit un nouveau message privé
 */
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
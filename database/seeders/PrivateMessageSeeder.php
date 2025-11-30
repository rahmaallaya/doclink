<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrivateMessage;

class PrivateMessageSeeder extends Seeder
{
    public function run(): void
    {
        // Message du patient 2 au médecin 1
        PrivateMessage::create([
            'sender_id' => 2, // Patient Paul
            'receiver_id' => 1, // Dr. Jean Dupont
            'subject' => 'Question sur mon traitement',
            'message' => 'Bonjour Docteur, j\'aimerais savoir si je peux continuer mon traitement actuel.',
            'read' => false,
            'created_at' => now()->subHours(2),
        ]);

        // Réponse du médecin
        PrivateMessage::create([
            'sender_id' => 1, // Dr. Jean Dupont
            'receiver_id' => 2, // Patient Paul
            'subject' => 'RE: Question sur mon traitement',
            'message' => 'Bonjour, oui vous pouvez continuer votre traitement. Nous ferons un point lors de votre prochain rendez-vous.',
            'read' => true,
            'read_at' => now()->subHour(),
            'created_at' => now()->subHour(),
        ]);
    }
}
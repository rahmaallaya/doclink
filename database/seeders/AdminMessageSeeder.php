<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminMessage;

class AdminMessageSeeder extends Seeder
{
    public function run(): void
    {
        AdminMessage::create([
            'user_id' => 2, // Patient Paul
            'subject' => 'Problème de paiement',
            'message' => 'Bonjour, j\'ai un problème avec le paiement de ma dernière consultation.',
            'status' => 'open',
            'priority' => 'high',
            'created_at' => now()->subHours(3),
        ]);

        AdminMessage::create([
            'user_id' => 1, // Dr. Jean Dupont
            'subject' => 'Modification de profil',
            'message' => 'Je souhaiterais modifier mes horaires de disponibilité.',
            'status' => 'resolved',
            'priority' => 'low',
            'admin_response' => 'Vous pouvez modifier vos disponibilités directement depuis votre espace médecin.',
            'resolved_at' => now()->subHour(),
            'created_at' => now()->subDays(1),
        ]);
    }
}
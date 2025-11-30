<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Question 1
        $question1 = Question::create([
            'patient_id' => 2, // Patient Paul
            'category_id' => 1, // Cardiologie
            'title' => 'Douleurs thoraciques après effort',
            'content' => 'Je ressens des douleurs au niveau de la poitrine après avoir fait du sport. Est-ce normal ?',
            'status' => 'answered',
            'created_at' => now()->subDays(2),
        ]);

        // Réponse du cardiologue
        Answer::create([
            'question_id' => $question1->id,
            'doctor_id' => 1, // Dr. Jean Dupont (Cardiologue)
            'content' => 'Les douleurs thoraciques après effort peuvent avoir plusieurs causes. Je vous recommande de consulter rapidement pour un examen approfondi, notamment un électrocardiogramme.',
            'is_verified' => true,
            'helpful_count' => 3,
            'created_at' => now()->subDay(),
        ]);

        // Question 2
        $question2 = Question::create([
            'patient_id' => 4, // Patient Sophie
            'category_id' => 2, // Dermatologie
            'title' => 'Rougeurs sur le visage',
            'content' => 'J\'ai des rougeurs persistantes sur les joues depuis quelques semaines. Que faire ?',
            'status' => 'open',
            'created_at' => now()->subHours(5),
        ]);
    }
}
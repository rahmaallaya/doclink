<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionCategory;

class QuestionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cardiologie',
                'description' => 'Questions relatives au cœur et au système cardiovasculaire',
                'slug' => 'cardiologie',
            ],
            [
                'name' => 'Dermatologie',
                'description' => 'Questions relatives à la peau, aux cheveux et aux ongles',
                'slug' => 'dermatologie',
            ],
            [
                'name' => 'Médecine Générale',
                'description' => 'Questions générales de santé',
                'slug' => 'medecine-generale',
            ],
            [
                'name' => 'Pédiatrie',
                'description' => 'Questions relatives à la santé des enfants',
                'slug' => 'pediatrie',
            ],
            [
                'name' => 'Psychiatrie',
                'description' => 'Questions relatives à la santé mentale',
                'slug' => 'psychiatrie',
            ],
        ];

        foreach ($categories as $category) {
            QuestionCategory::create($category);
        }
    }
}
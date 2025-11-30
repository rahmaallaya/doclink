<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AvailabilitySeeder::class,
            AppointmentSeeder::class,
            
            // Sprint 2
            QuestionCategorySeeder::class,
            PrivateMessageSeeder::class,
            QuestionSeeder::class,
            AdminMessageSeeder::class,
        ]);
    }
}
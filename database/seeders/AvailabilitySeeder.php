<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Availability; // Ajouter cette ligne ici

class AvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Availability::create([
            'doctor_id' => 1,
            'start_time' => '2025-10-21 09:00:00',
            'end_time' => '2025-10-21 10:00:00',
            'booked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Availability::create([
            'doctor_id' => 1,
            'start_time' => '2025-10-22 14:00:00',
            'end_time' => '2025-10-22 15:00:00',
            'booked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Disponibilités pour Dr. Marie Lefevre (ID 3)
        Availability::create([
            'doctor_id' => 3,
            'start_time' => '2025-10-21 10:00:00',
            'end_time' => '2025-10-21 11:00:00',
            'booked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Availability::create([
            'doctor_id' => 3,
            'start_time' => '2025-10-23 09:00:00',
            'end_time' => '2025-10-23 10:00:00',
            'booked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Availability; // Ajouter cette ligne ici
use App\Models\Appointment; // Cette ligne est déjà nécessaire pour Appointment

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer une disponibilité existante pour la réserver
        $availability = Availability::where('doctor_id', 1)->where('booked', false)->first();

        if ($availability) {
            $availability->booked = true;
            $availability->save();

            Appointment::create([
                'patient_id' => 2, // Patient Paul
                'doctor_id' => 1,  // Dr. Jean Dupont
                'availability_id' => $availability->id,
                'appointment_time' => $availability->start_time,
                'status' => 'planned',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Exemple d'annulation
        $cancelledAvailability = Availability::where('doctor_id', 3)->where('booked', false)->first();

        if ($cancelledAvailability) {
            $cancelledAvailability->booked = true;
            $cancelledAvailability->save();

            Appointment::create([
                'patient_id' => 4, // Patient Sophie
                'doctor_id' => 3,  // Dr. Marie Lefevre
                'availability_id' => $cancelledAvailability->id,
                'appointment_time' => $cancelledAvailability->start_time,
                'status' => 'cancelled',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
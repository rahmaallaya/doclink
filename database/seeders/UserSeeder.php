<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer uniquement si n'existe pas
        User::firstOrCreate(
            ['email' => 'jean.dupont@example.com'],
            [
                'name' => 'Dr. Jean Dupont',
                'role' => 'medecin',
                'specialty' => 'Cardiologie',
                'location' => 'Paris',
            ]
        );

        User::firstOrCreate(
            ['email' => 'paul@example.com'],
            [
                'name' => 'Patient Paul',
                'role' => 'patient',
            ]
        );

        User::firstOrCreate(
            ['email' => 'marie.lefevre@example.com'],
            [
                'name' => 'Dr. Marie Lefevre',
                'role' => 'medecin',
                'specialty' => 'Dermatologie',
                'location' => 'Lyon',
            ]
        );

        User::firstOrCreate(
            ['email' => 'sophie@example.com'],
            [
                'name' => 'Patient Sophie',
                'role' => 'patient',
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@doclink.com'],
            [
                'name' => 'Administrateur',
                'role' => 'admin',
            ]
        );
    }
}
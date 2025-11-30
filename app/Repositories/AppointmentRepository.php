<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function searchDoctors(string $specialty, string $location): Collection
    {
        return User::where('role', 'medecin')
            ->where('specialty', 'like', "%$specialty%")
            ->where('location', 'like', "%$location%")
            ->get();
    }

    public function getAppointments($userId, $role)
    {
        return Appointment::where('user_id', $userId)->get();
    }

    public function getAvailabilities($doctorId)
    {
        return Availability::where('doctor_id', $doctorId)->get();
    }

public function bookAppointment(string $selectedTime, int $patientId, int $doctorId, int $availabilityId): Appointment
{
    $availability = Availability::findOrFail($availabilityId);
    if ($availability->doctor_id !== $doctorId || $availability->booked) {
        throw new \Exception('Disponibilité non valide ou déjà réservée.');
    }

    $appointment = Appointment::create([
        'user_id' => $patientId,
        'doctor_id' => $doctorId,
        'availability_id' => $availabilityId,
        'appointment_time' => $availability->start_time, // Début de l'intervalle
        'end_time' => $availability->end_time, // Fin de l'intervalle
        'status' => 'planned',
    ]);

    $availability->update(['booked' => true]);

    return $appointment;
}
    public function getHistory(int $userId, string $role)
    {
        if ($role === 'medecin') {
            return Appointment::where('doctor_id', $userId)->get();
        }
        return Appointment::where('user_id', $userId)->get();
    }

    public function getAgenda(int $doctorId)
    {
        return Appointment::where('doctor_id', $doctorId)->where('status', 'planned')->get();
    }

    public function getTodayAgenda(int $doctorId)
    {
        return Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_time', Carbon::today())
            ->where('status', 'planned')
            ->get();
    }

    public function addAvailability(int $doctorId, array $data)
    {
        return Availability::create([
            'doctor_id' => $doctorId,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'booked' => false,
        ]);
    }

    public function updateAvailability(int $availabilityId, array $data)
    {
        $availability = Availability::findOrFail($availabilityId);
        $availability->update($data);
        return $availability;
    }

    public function deleteAvailability(int $availabilityId)
    {
        Availability::findOrFail($availabilityId)->delete();
    }

    public function getAppointment($appointmentId)
    {
        return Appointment::find($appointmentId);
    }

    // Afficher directement les intervalles de disponibilité existants dans la table
    public function getAvailableSlots(int $doctorId): array
    {
        $availabilities = Availability::where('doctor_id', $doctorId)
            ->where('booked', false) // Seulement les disponibilités non réservées
            ->get();

        $slots = [];
        foreach ($availabilities as $availability) {
            $slots[] = [
                'id' => $availability->id,
                'start_time' => $availability->start_time,
                'end_time' => $availability->end_time,
                'available' => true, // Puisque booked = false
                'availability_id' => $availability->id,
            ];
        }

        return $slots;
    }

    // Pour la modale - diviser l'intervalle en créneaux horaires si nécessaire
    public function getAvailableTimes(int $doctorId, $startTime, $endTime, $availabilityId): array
    {
        $availability = Availability::findOrFail($availabilityId);
        if ($availability->doctor_id !== $doctorId || $availability->booked) {
            return [];
        }

        $slots = [];
        $current = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        while ($current->clone()->addHour() <= $end) {
            $slotEnd = $current->clone()->addHour();
            $overlapping = Appointment::where('doctor_id', $doctorId)
                ->where('status', '!=', 'cancelled')
                ->where('appointment_time', '<', $slotEnd)
                ->whereRaw("DATE_ADD(appointment_time, INTERVAL 1 HOUR) > ?", [$current])
                ->exists();

            if (!$overlapping) {
                $slots[] = [
                    'time' => $current->format('Y-m-d H:i:s'),
                    'available' => true,
                ];
            }

            $current->addHour();
        }

        return $slots;
    }
}
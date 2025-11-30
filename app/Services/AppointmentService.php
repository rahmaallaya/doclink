<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Availability; // CORRECTION : Utiliser le bon namespace
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function getAvailableSlots($doctorId)
    {
        $availabilities = Availability::where('doctor_id', $doctorId)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();

        $groupedSlots = [];

        foreach ($availabilities as $availability) {
            $date = Carbon::parse($availability->start_time)->format('Y-m-d');
            
            // Vérifier si le créneau est déjà réservé
            $isBooked = Appointment::where('availability_id', $availability->id)
                                 ->where('status', 'planned')
                                 ->exists();

            if (!isset($groupedSlots[$date])) {
                $groupedSlots[$date] = [];
            }
            
            $groupedSlots[$date][] = [
                'availability_id' => $availability->id,
                'start' => $availability->start_time,
                'end' => $availability->end_time,
                'is_booked' => $isBooked
            ];
        }

        return $groupedSlots;
    }

    public function getAppointments($userId, $role)
    {
        if ($role === 'patient') {
            return Appointment::with('doctor')
                ->where('user_id', $userId)
                ->orderBy('appointment_time', 'desc')
                ->get();
        }

        return Appointment::with('user')
            ->where('doctor_id', $userId)
            ->orderBy('appointment_time', 'desc')
            ->get();
    }

    public function getAgenda($doctorId)
    {
        return Appointment::with('user')
            ->where('doctor_id', $doctorId)
            ->orderBy('appointment_time', 'asc')
            ->get();
    }

    public function getTodayAgenda($doctorId)
    {
        $today = Carbon::today();

        return Appointment::with('user')
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_time', $today)
            ->where('status', 'planned')
            ->orderBy('appointment_time', 'asc')
            ->get();
    }

    public function cancelAppointment($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update(['status' => 'cancelled']);
        
        return $appointment;
    }

    public function isSlotAvailable($availabilityId)
    {
        return !Appointment::where('availability_id', $availabilityId)
                           ->whereIn('status', ['planned', 'confirmed'])
                           ->exists();
    }

    // Supprimez ou corrigez cette méthode si elle existe
    public function manageAvailability(int $doctorId, array $data, string $action)
    {
        // Si vous avez besoin de cette méthode, utilisez le bon modèle :
        if ($action === 'add') {
            Availability::create([
                'doctor_id' => $doctorId,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);
        } elseif ($action === 'update' && isset($data['availability_id'])) {
            $availability = Availability::find($data['availability_id']);
            if ($availability) {
                $availability->update([
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                ]);
            }
        } elseif ($action === 'delete' && isset($data['availability_id'])) {
            $availability = Availability::find($data['availability_id']);
            if ($availability && !$availability->appointment) {
                $availability->delete();
            }
        }
    }
}
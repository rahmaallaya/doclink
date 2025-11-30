<?php

namespace App\Repositories\Interfaces;;

use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Illuminate\Support\Collection;

interface AppointmentRepositoryInterface
{
    public function searchDoctors(string $specialty, string $location): Collection;
    public function getAppointments($userId, $role);
    public function getAvailabilities($doctorId);
    public function bookAppointment(string $selectedTime, int $patientId, int $doctorId, int $availabilityId): Appointment;
    public function getHistory(int $userId, string $role);
    public function getAgenda(int $doctorId);
    public function getTodayAgenda(int $doctorId);
    public function addAvailability(int $doctorId, array $data);
    public function updateAvailability(int $availabilityId, array $data);
    public function deleteAvailability(int $availabilityId);
    public function getAppointment($appointmentId);
    public function getAvailableSlots(int $doctorId): array;
    public function getAvailableTimes(int $doctorId, $startTime, $endTime, $availabilityId): array;
}
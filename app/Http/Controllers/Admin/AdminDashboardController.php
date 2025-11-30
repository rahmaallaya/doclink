<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Question;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistiques
        $stats = [
            'total_patients'     => User::where('role', 'patient')->count(),
            'total_medecins'     => User::where('role', 'medecin')->where('status', 'ACTIVE')->count(),
            'pending_medecins'   => User::where('role', 'medecin')->where('status', 'PENDING_VALIDATION')->count(),
            'total_rdv'          => Appointment::count(),
            'rdv_today'          => Appointment::whereDate('appointment_time', today())->count(),
            'total_questions'    => Question::count(),
        ];

        // Données modales
        $patients = User::where('role', 'patient')
                        ->select('name', 'email', 'created_at')
                        ->latest()
                        ->get();

        $doctors = User::where('role', 'medecin')
                       ->where('status', 'ACTIVE')
                       ->select('name', 'specialty', 'location', 'created_at')
                       ->latest()
                       ->get();

        // Médecins en attente – SEULEMENT les colonnes qui existent vraiment
        $pendingDoctors = User::where('role', 'medecin')
                              ->where('status', 'PENDING_VALIDATION')
                              ->select('id', 'name', 'email', 'specialty', 'location', 'created_at')
                              ->latest()
                              ->get();

        $allAppointments = Appointment::with(['user:id,name', 'doctor:id,name'])
                                      ->select('user_id', 'doctor_id', 'appointment_time', 'status', 'created_at')
                                      ->latest()
                                      ->get();

        $recentAppointments = Appointment::with(['user:id,name', 'doctor:id,name'])
                                         ->latest()
                                         ->take(10)
                                         ->get();

        $recentQuestions = Question::with(['patient:id,name'])
                                   ->select('id', 'patient_id', 'title', 'created_at')
                                   ->latest()
                                   ->take(10)
                                   ->get();

        return view('admin.dashboard', compact(
            'stats',
            'pendingDoctors',
            'recentAppointments',
            'recentQuestions',
            'patients',
            'doctors',
            'allAppointments'
        ));
    }

    // Valider un médecin
    public function approveDoctor($id)
    {
        $doctor = User::where('role', 'medecin')->findOrFail($id);
        $doctor->status = 'ACTIVE';
        $doctor->save();

        return back()->with('success', 'Dr. ' . $doctor->name . ' a été validé avec succès !');
    }

    // Rejeter un médecin
    public function rejectDoctor($id)
    {
        $doctor = User::where('role', 'medecin')->findOrFail($id);
        $doctor->status = 'REJECTED';
        $doctor->save();

        return back()->with('success', 'La demande du Dr. ' . $doctor->name . ' a été refusée.');
    }
}
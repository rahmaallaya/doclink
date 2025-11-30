<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $patients = User::where('role', 'patient')->latest()->get();
        $doctors  = User::where('role', 'medecin')->latest()->get();
        $pending  = User::where('role', 'medecin')
                        ->where('status', 'PENDING_VALIDATION')
                        ->latest()
                        ->get();

        $stats = [
            'total_users'     => User::count(),
            'total_patients'  => $patients->count(),
            'total_doctors'   => $doctors->where('status', 'ACTIVE')->count(),
            'pending_doctors' => $pending->count(),
            'suspended'       => User::where('status', 'SUSPENDED')->count(),
        ];

        return view('admin.users.index', compact('patients', 'doctors', 'pending', 'stats'));
    }

    public function approveDoctor($id)
    {
        $user = User::where('role', 'medecin')->findOrFail($id);
        $user->status = 'ACTIVE';
        $user->save();

        return back()->with('success', "Dr. {$user->name} a été approuvé avec succès !");
    }

    public function rejectDoctor($id)
    {
        $user = User::where('role', 'medecin')->findOrFail($id);
        $user->status = 'REJECTED';
        $user->save();

        return back()->with('success', "La demande du Dr. {$user->name} a été refusée.");
    }

    public function suspend($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'SUSPENDED';
        $user->save();

        return back()->with('success', "Compte de {$user->name} suspendu.");
    }

    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'ACTIVE';
        $user->save();

        return back()->with('success', "Compte de {$user->name} réactivé.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete(); // ou soft delete

        return back()->with('success', "Compte de {$name} supprimé définitivement.");
    }
}
<?php
// app/Http/Controllers/DoctorProfileController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DoctorProfileController extends Controller
{
    public function show($id)
    {
        $doctor = User::where('role', 'medecin')
                     ->where('status', 'ACTIVE')
                     ->with(['availabilities' => function($query) {
                         $query->where('start_time', '>', now())
                               ->orderBy('start_time')
                               ->limit(10);
                     }])
                     ->findOrFail($id);
        
        return view('doctors.profile', compact('doctor'));
    }
}
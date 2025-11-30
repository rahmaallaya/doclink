<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // ==================== INSCRIPTION ====================
    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:8|confirmed',
            'role'       => 'required|in:patient,medecin',
            'specialty'  => 'required_if:role,medecin|string|max:120|nullable',
            'location'   => 'required_if:role,medecin|string|max:120|nullable',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'specialty' => $request->role === 'medecin' ? $request->specialty : null,
            'location'  => $request->role === 'medecin' ? $request->location : null,
            'status'    => $request->role === 'patient' ? 'ACTIVE' : 'PENDING_VALIDATION',
        ]);

        if ($request->role === 'patient') {
            Auth::login($user);
            return redirect('/')->with('success', 'Bienvenue sur DocLink ! Votre compte est actif.');
        }

        // Médecin → en attente
        return redirect('/login')->with('info', '
            Votre demande a bien été envoyée !
            Un administrateur validera votre compte sous 24h.
            Vous recevrez un email dès qu’il sera activé.
        ');
    }

    // ==================== CONNEXION ====================
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // Compte en attente de validation (médecins)
            if ($user->status === 'PENDING_VALIDATION') {
                Auth::logout();
                return back()->with('error', '
                    Votre compte est en attente de validation par l’administrateur.
                    Vous serez notifié par email dès qu’il sera activé.
                ');
            }

            // Compte bloqué ou autre statut (au cas où tu ajoutes plus tard)
            if ($user->status !== 'ACTIVE') {
                Auth::logout();
                return back()->with('error', 'Votre compte n’est pas encore activé.');
            }

            $request->session()->regenerate();

            // Redirection selon le rôle
            return redirect()->intended(
                $user->role === 'admin' ? '/admin/dashboard' : '/appointments/search'
            );
        }

        return back()->with('error', 'Email ou mot de passe incorrect.');
    }

    // ==================== DÉCONNEXION ====================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Vous êtes déconnecté.');
    }
}
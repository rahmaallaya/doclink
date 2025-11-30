<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, $roles, true)) {
            return redirect()->back()->with('error', 'Accès refusé.');
        }
        // Bloquer comptes suspendus / médecins non actifs
        if ($user->status === 'SUSPENDED' || ($user->role === 'medecin' && $user->status !== 'ACTIVE')) {
            return redirect()->back()->with('error', 'Votre compte n’est pas actif.');
        }
        return $next($request);
    }
}

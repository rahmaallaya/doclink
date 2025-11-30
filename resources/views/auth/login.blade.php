{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')
@section('title', 'Connexion - DocLink')

@section('content')
<div class="container py-5" style="max-width: 480px; margin-top: 100px;">
    <div class="card shadow-lg border-0">
        <div class="card-body p-5">
            <h2 class="text-center mb-4 fw-bold text-primary">Connexion</h2>

            {{-- MESSAGE DE SUCCÈS (inscription médecin ou autre) --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- MESSAGE INFO (médecin en attente de validation) --}}
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-clock me-2"></i>
                    <strong>Demande envoyée !</strong><br>
                    {{ session('info') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- MESSAGE D'ERREUR (compte en attente / mauvais identifiants) --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Formulaire de connexion --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                           placeholder="Email" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <input type="password" name="password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                           placeholder="Mot de passe" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Rester connecté</label>
                    </div>
                    {{-- Tu peux ajouter un lien mot de passe oublié plus tard --}}
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                    Se connecter
                </button>
            </form>

            <hr class="my-5">

            <div class="text-center">
                <p class="mb-0">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">
                        S'inscrire gratuitement
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
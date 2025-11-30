@extends('layouts.app')
@section('title', 'Inscription Médecin')

@section('content')
<div class="container" style="max-width: 580px; margin-top: 70px;">
    <div class="card shadow-lg">
        <div class="card-body p-5">
            <h2 class="text-center mb-4">Inscription Médecin</h2>

            <form action="{{ route('register.doctor') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nom complet</label>
                    <input type="text" name="name" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Spécialité</label>
                    <input type="text" name="specialty" class="form-control form-control-lg" placeholder="Ex: Cardiologue, Généraliste..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ville / Région</label>
                    <input type="text" name="location" class="form-control form-control-lg" placeholder="Ex: Paris, Tunis..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email professionnel</label>
                    <input type="email" name="email" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-lg" required>
                </div>
                <button class="btn btn-primary btn-lg w-100">Envoyer ma demande</button>
            </form>

            <div class="alert alert-info mt-4 text-center">
                Votre compte sera activé par un administrateur sous 24h.
            </div>
        </div>
    </div>
</div>
@endsection
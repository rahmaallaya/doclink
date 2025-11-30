@extends('layouts.app')
@section('title', 'Inscription')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0">
                <div class="card-body p-5">

                    <h2 class="text-center mb-4 fw-bold text-primary">Créer un compte DocLink</h2>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom complet</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Mot de passe</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Confirmer</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Je suis</label>
                            <select name="role" id="role" class="form-select form-select-lg" required>
                                <option value="">Choisir mon profil...</option>
                                <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                                <option value="medecin" {{ old('role') == 'medecin' ? 'selected' : '' }}>Médecin</option>
                            </select>
                        </div>

                        <!-- CHAMPS MÉDECIN (cachés pour les patients) -->
                        <div id="medecin-fields" class="{{ old('role') === 'medecin' ? '' : 'd-none' }} border-top pt-4">
                            <h5 class="text-primary mb-3">Informations professionnelles</h5>

                            <div class="mb-3">
                                <label class="form-label">Spécialité</label>
                                <input type="text" name="specialty" class="form-control" 
                                       value="{{ old('specialty') }}" placeholder="ex: Cardiologue, Dermatologue..." 
                                       {{ old('role') === 'medecin' ? 'required' : '' }}>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ville / Région</label>
                                <input type="text" name="location" class="form-control" 
                                       value="{{ old('location') }}" placeholder="ex: Paris, Lyon, Marseille..." 
                                       {{ old('role') === 'medecin' ? 'required' : '' }}>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 mt-4">
                            S'inscrire
                        </button>
                    </form>

                    <p class="text-center mt-4 text-muted">
                        Déjà inscrit ? <a href="{{ route('login') }}" class="text-primary fw-bold">Se connecter</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('role').addEventListener('change', function () {
        const medecinFields = document.getElementById('medecin-fields');
        const isMedecin = this.value === 'medecin';

        medecinFields.classList.toggle('d-none', !isMedecin);

        // Ajouter/supprimer l'attribut required dynamiquement
        medecinFields.querySelectorAll('input').forEach(input => {
            if (isMedecin) {
                input.setAttribute('required', 'required');
            } else {
                input.removeAttribute('required');
            }
        });
    });

    // Au chargement (si erreur de validation, on réaffiche les champs médecin)
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('role').value === 'medecin') {
            document.getElementById('medecin-fields').classList.remove('d-none');
        }
    });
</script>
@endsection
{{-- resources/views/appointments/search.blade.php --}}
@extends('layouts.app')
@section('title', 'Rechercher un Médecin')

@section('content')
<div class="container py-5">

    <h1 class="h2 fw-bold text-primary mb-4">Trouver un médecin</h1>

    <!-- Formulaire de recherche -->
    <form method="GET" action="{{ route('appointments.search') }}" class="mb-5">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="specialty" class="form-control form-control-lg" 
                       placeholder="Spécialité (ex: Cardiologue)" value="{{ request('specialty') }}">
            </div>
            <div class="col-md-4">
                <input type="text" name="location" class="form-control form-control-lg" 
                       placeholder="Ville / Région (ex: Paris)" value="{{ request('location') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </div>
        </div>
    </form>

    <!-- Résultats -->
    @forelse($doctors as $doctor)
        <div class="card mb-3 shadow-sm hover-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <!-- Photo du médecin -->
                    <div class="col-auto">
                        <img src="{{ $doctor->avatar }}" 
                             alt="Dr. {{ $doctor->name }}"
                             class="rounded-circle border border-2 border-primary"
                             style="width: 80px; height: 80px; object-fit: cover;">
                    </div>

                    <!-- Informations -->
                    <div class="col">
                        <h5 class="mb-1 fw-bold text-primary">
                            Dr. {{ $doctor->name }}
                            <i class="fas fa-check-circle text-success ms-1" title="Vérifié"></i>
                        </h5>
                        <p class="mb-2 text-muted">
                            <i class="fas fa-stethoscope"></i> {{ $doctor->specialty ?? 'Généraliste' }}
                            <span class="mx-2">•</span>
                            <i class="fas fa-map-marker-alt"></i> {{ $doctor->location ?? 'Non renseigné' }}
                        </p>
                        @if($doctor->bio)
                        <p class="mb-0 text-muted small">
                            {{ Str::limit($doctor->bio, 100) }}
                        </p>
                        @endif
                    </div>

                    <!-- Boutons d'action -->
                    <div class="col-auto">
                        <div class="d-flex flex-column gap-2">
                            <!-- Bouton profil (toujours visible) -->
                            <a href="{{ route('doctor.profile', $doctor->id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-user"></i> Voir le profil
                            </a>

                            <!-- Bouton RDV (UNIQUEMENT pour les patients) -->
                            @if(auth()->check() && auth()->user()->role === 'patient')
                                <a href="{{ route('appointments.availabilities', $doctor->id) }}" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-calendar-check"></i> Prendre RDV
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <p class="text-muted fs-4">Aucun médecin trouvé avec ces critères.</p>
            <a href="{{ route('appointments.search') }}" class="btn btn-outline-primary">
                Réinitialiser la recherche
            </a>
        </div>
    @endforelse
</div>

<style>
.hover-card {
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    border-color: #007bff;
}
</style>
@endsection
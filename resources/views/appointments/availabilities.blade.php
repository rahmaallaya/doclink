@extends('layouts.app')
@section('title', 'Disponibilités du Docteur')

@section('content')
<div class="container py-5">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 fw-bold text-primary">Disponibilités du Dr. {{ $doctor->name }}</h1>
            <p class="text-muted">
                Spécialité : <strong>{{ $doctor->specialty }}</strong> | 
                Localisation : <strong>{{ $doctor->location }}</strong>
            </p>
        </div>
        <a href="{{ route('appointments.search') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Retour à la recherche
        </a>
    </div>

    <!-- Créneaux disponibles -->
    @if(count($availableSlots) > 0)
        @foreach($availableSlots as $date => $slots)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-date"></i>
                        {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($slots as $slot)
                            @php
                                // Vérification sécurisée des clés du tableau
                                $availabilityId = $slot['availability_id'] ?? null;
                                $start = $slot['start'] ?? null;
                                $end = $slot['end'] ?? null;
                                
                                if (!$availabilityId || !$start || !$end) {
                                    continue; // Passe au slot suivant si données incomplètes
                                }
                                
                                $isBooked = \App\Models\Appointment::where('availability_id', $availabilityId)
                                                                  ->where('status', 'planned')
                                                                  ->exists();
                                $startTime = \Carbon\Carbon::parse($start);
                                $isPast = $startTime->isPast();
                            @endphp
                            
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                <form action="{{ route('appointments.book', $doctorId) }}" method="POST"
                                      class="d-grid h-100 {{ $isBooked || $isPast ? 'opacity-50' : '' }}">
                                    @csrf
                                    <input type="hidden" name="availability_id" value="{{ $availabilityId }}">
                                    <input type="hidden" name="start_time" value="{{ $start }}">
                                    <input type="hidden" name="end_time" value="{{ $end }}">
                                    
                                    <button type="submit" 
                                            class="btn {{ $isBooked ? 'btn-secondary' : ($isPast ? 'btn-warning' : 'btn-outline-primary') }} btn-lg py-3 h-100"
                                            {{ $isBooked || $isPast ? 'disabled' : '' }}>
                                        <div class="fw-bold fs-5">{{ $startTime->format('H:i') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($end)->format('H:i') }}</small>
                                        
                                        @if($isBooked)
                                            <div class="mt-2">
                                                <small class="text-danger fw-bold">
                                                    <i class="bi bi-x-circle"></i> Déjà réservé
                                                </small>
                                            </div>
                                        @elseif($isPast)
                                            <div class="mt-2">
                                                <small class="text-dark fw-bold">
                                                    <i class="bi bi-clock-history"></i> Créneau passé
                                                </small>
                                            </div>
                                        @else
                                            <div class="mt-2">
                                                <small class="text-success fw-bold">
                                                    <i class="bi bi-check-circle"></i> Disponible
                                                </small>
                                            </div>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Aucun créneau disponible -->
        <div class="text-center py-5 bg-light rounded">
            <i class="bi bi-calendar-x display-1 text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Aucune disponibilité pour le moment</h3>
            <p class="text-muted mb-4">
                Ce médecin n'a pas encore défini ses créneaux de disponibilité.
            </p>
            <a href="{{ route('appointments.search') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Retour à la recherche
            </a>
        </div>
    @endif
</div>

<style>
.opacity-50 {
    opacity: 0.6;
}
.btn:disabled {
    cursor: not-allowed;
}
.card {
    transition: transform 0.2s ease;
}
.card:hover {
    transform: translateY(-2px);
}
</style>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@endsection
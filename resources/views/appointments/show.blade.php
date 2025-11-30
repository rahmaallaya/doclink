{{-- resources/views/appointments/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détail du rendez-vous')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-calendar-check fa-fw me-2"></i>
                        Détail du rendez-vous
                    </h3>
                </div>

                <div class="card-body p-5">

                    <div class="row g-4 text-center text-md-start">
                        <div class="col-md-6">
                            <h5><i class="fas fa-user me-2 text-primary"></i> Patient</h5>
                            <p class="lead">{{ $appointment->patient->name }}</p>
                        </div>

                        <div class="col-md-6">
                            <h5><i class="fas fa-user-md me-2 text-success"></i> Médecin</h5>
                            <p class="lead">
                                Dr. {{ $appointment->doctor->name }}
                                <small class="text-muted d-block">
                                    ({{ $appointment->doctor->specialty ?? 'Spécialité non définie' }})
                                </small>
                            </p>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="row g-4 text-center">
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-4">
                                <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                                <h6>Date et heure</h6>
                                <p class="fw-bold fs-5 mb-0">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y') }}
                                    <br>
                                    <span class="text-primary">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-4">
                                <i class="fas fa-info-circle fa-2x text-info mb-3"></i>
                                <h6>Statut</h6>
                                <span class="badge fs-6 px-4 py-3 bg-{{ 
                                    $appointment->status === 'planned' ? 'success' : 
                                    ($appointment->status === 'cancelled' ? 'danger' : 'secondary')
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-4">
                                <i class="fas fa-calendar-plus fa-2x text-muted mb-3"></i>
                                <h6>Créé le</h6>
                                <p class="mb-0">
                                    {{ $appointment->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="text-center">
                        @if($appointment->status === 'planned' && auth()->id() === $appointment->user_id)
                            <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg px-5"
                                        onclick="return confirm('Voulez-vous vraiment annuler ce rendez-vous ?')">
                                    <i class="fas fa-times me-2"></i> Annuler le rendez-vous
                                </button>
                            </form>
                            <br><br>
                        @endif

                        <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary btn-lg px-5">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour à mes rendez-vous
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3) !important;
    }
</style>
@endpush
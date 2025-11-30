{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Dashboard Admin - DocLink')

@section('content')
<div class="container-fluid py-5">
    <h1 class="h2 fw-bold text-primary mb-4">Dashboard Administrateur</h1>
    <p class="text-muted fs-5 mb-5">Bienvenue, <strong>{{ Auth::user()->name }}</strong></p>

    <!-- 4 cartes cliquables -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-lg border-0 rounded-4 stat-card cursor-pointer" data-bs-toggle="modal" data-bs-target="#patientsModal">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 fw-bold">{{ $stats['total_patients'] }}</h1>
                    <p class="fs-5 mb-0">Patients inscrits</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white shadow-lg border-0 rounded-4 stat-card cursor-pointer" data-bs-toggle="modal" data-bs-target="#doctorsModal">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 fw-bold">{{ $stats['total_medecins'] }}</h1>
                    <p class="fs-5 mb-0">Médecins actifs</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-lg border-0 rounded-4 stat-card cursor-pointer" data-bs-toggle="modal" data-bs-target="#pendingModal">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 fw-bold">{{ $stats['pending_medecins'] }}</h1>
                    <p class="fs-5 mb-0">En attente validation</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white shadow-lg border-0 rounded-4 stat-card cursor-pointer" data-bs-toggle="modal" data-bs-target="#appointmentsModal">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 fw-bold">{{ $stats['total_rdv'] }}</h1>
                    <p class="fs-5 mb-0">Rendez-vous pris</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers RDV + Questions -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light"><h5>Derniers rendez-vous</h5></div>
                <div class="card-body">
                    @forelse($recentAppointments as $appt)
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span><strong>{{ $appt->user->name }}</strong> → Dr. {{ $appt->doctor->name }}</span>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('d/m/Y H:i') }}</small>
                        </div>
                    @empty
                        <p class="text-muted">Aucun rendez-vous récent</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-light"><h5>Dernières questions forum</h5></div>
                <div class="card-body">
                    @forelse($recentQuestions as $q)
                        <div class="py-2 border-bottom">
                            <strong>{{ $q->patient?->name ?? 'Patient supprimé' }} :</strong>
                            {{ Str::limit($q->title, 60) }}
                        </div>
                    @empty
                        <p class="text-muted">Aucune question récente</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toutes les modales -->
@include('admin.partials.dashboard-modals')

<style>
    .stat-card {
        transition: all 0.3s ease;
        transform: translateY(0);
    }
    .stat-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.2) !important;
    }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection
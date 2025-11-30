@extends('layouts.app')
@section('title', 'Mes Rendez-vous')

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 fw-bold text-primary">Mes rendez-vous</h1>
            <p class="text-muted fs-5">
                Bonjour <strong>{{ $user->name }}</strong>
                <span class="badge bg-success ms-2">{{ ucfirst($user->role) }}</span>
            </p>
        </div>
        @if($user->role === 'patient')
            <a href="{{ route('appointments.search') }}" class="btn btn-primary btn-lg">
                Prendre un rendez-vous
            </a>
        @endif
    </div>

    @if($user->role === 'medecin')
        <div class="card shadow mb-4 border-primary">
            <div class="card-body text-center py-4 bg-light">
                <h4 class="mb-3">Gérer vos disponibilités</h4>
                <a href="{{ route('appointments.manage_availabilities') }}" class="btn btn-outline-primary btn-lg">
                    Ajouter ou modifier mes créneaux
                </a>
            </div>
        </div>
    @endif

    <h3 class="mb-4">
        @if($user->role === 'patient') Vos rendez-vous
        @else Rendez-vous de vos patients
        @endif
    </h3>

    @forelse($history as $appointment)
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">
                        @if($user->role === 'patient')
                            Dr. {{ $appointment->doctor->name }}
                        @else
                            {{ $appointment->user->name }}
                        @endif
                    </h5>
                    <p class="mb-1 text-muted">
                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y à H\hi') }}
                    </p>
                    <span class="badge bg-{{ $appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning') }}">
                        {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                    </span>
                </div>

                @if($appointment->status === 'planned')
                    <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Annuler ce rendez-vous ?')">
                            Annuler
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-5 bg-light rounded">
            <i class="far fa-calendar-times fa-5x text-muted mb-4"></i>
            <p class="fs-4 text-muted">Aucun rendez-vous pour le moment</p>
            @if($user->role === 'patient')
                <a href="{{ route('appointments.search') }}" class="btn btn-primary">Trouver un médecin</a>
            @endif
        </div>
    @endforelse
</div>
@endsection
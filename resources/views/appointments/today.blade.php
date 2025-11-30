@extends('layouts.app')
@section('title', "Rendez-vous d'aujourd'hui")

@section('content')
<div class="container py-5">
    <h1 class="h3 fw-bold text-primary mb-4">
        Rendez-vous du {{ now()->format('d/m/Y') }}
    </h1>

    @forelse($todayAgenda as $appt)
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5>{{ $appt->user->name }}</h5>
                    <p class="mb-0 text-muted">
                        {{ \Carbon\Carbon::parse($appt->appointment_time)->format('H\hi') }}
                    </p>
                </div>
                <div>
                    <span class="badge bg-{{ $appt->status === 'confirmed' ? 'success' : 'warning' }}">
                        {{ ucfirst($appt->status) }}
                    </span>
                    @if($appt->status === 'planned')
                        <form action="{{ route('appointments.cancel', $appt->id) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Annuler
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="far fa-smile fa-4x text-success mb-3"></i>
            <p class="fs-3">Aucun rendez-vous aujourd'hui</p>
            <p class="text-muted">Profitez-en pour vous reposer !</p>
        </div>
    @endforelse
</div>
@endsection
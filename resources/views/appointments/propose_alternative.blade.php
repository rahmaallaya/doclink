{{-- resources/views/appointments/propose_alternative.blade.php --}}
@extends('layouts.app')
@section('title', 'Proposer un nouveau créneau')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-warning text-dark py-4 text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-calendar-alt"></i>
                        Proposer un nouveau rendez-vous
                    </h3>
                </div>

                <div class="card-body p-5">
                    <!-- Infos du RDV annulé -->
                    <div class="alert alert-info border-0 shadow-sm mb-5">
                        <h5 class="alert-heading">
                            <i class="fas fa-info-circle"></i> Rendez-vous annulé
                        </h5>
                        <p class="mb-2"><strong>Patient :</strong> {{ $appointment->user->name }}</p>
                        <p class="mb-0"><strong>Créneau initial :</strong> 
                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                    <form action="{{ route('appointments.send_alternative', $appointment->id) }}" method="POST">
                        @csrf

                        <h5 class="mb-4 fw-bold text-primary">Choisissez un nouveau créneau</h5>

                        @if(count($availableSlots) > 0)
                            @foreach($availableSlots as $date => $slots)
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">
                                    {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                </h6>
                                <div class="row g-3">
                                    @foreach($slots as $slot)
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" 
                                                   name="availability_id" 
                                                   id="slot-{{ $slot['availability_id'] }}"
                                                   value="{{ $slot['availability_id'] }}" required>
                                            <label class="form-check-label w-100" 
                                                   for="slot-{{ $slot['availability_id'] }}">
                                                <div class="border rounded p-3 text-center bg-light">
                                                    <strong class="text-primary">
                                                        {{ \Carbon\Carbon::parse($slot['start'])->format('H:i') }}
                                                    </strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($slot['end'])->format('H:i') }}
                                                    </small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach

                            <hr class="my-5">

                            <div class="mb-4">
                                <label class="form-label fw-bold">Message pour le patient (optionnel)</label>
                                <textarea name="message" class="form-control" rows="4" 
                                          placeholder="Ex: Désolé pour l'annulation, j'ai eu un imprévu..."></textarea>
                            </div>

                            <div class="d-flex gap-3 justify-content-end">
                                <a href="{{ route('appointments.today') }}" class="btn btn-outline-secondary btn-lg px-5">
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-paper-plane"></i> Envoyer la proposition
                                </button>
                            </div>

                        @else
                            <div class="alert alert-warning text-center py-5">
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                <h5>Aucun créneau disponible</h5>
                                <p class="mb-4">Vous devez d'abord ajouter des disponibilités.</p>
                                <a href="{{ route('appointments.manage_availabilities') }}" 
                                   class="btn btn-primary">
                                    Gérer mes disponibilités
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
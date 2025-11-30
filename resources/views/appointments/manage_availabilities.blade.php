@extends('layouts.app')
@section('title', 'Gérer mes disponibilités')

@section('content')
<div class="container py-5">

    <!-- En-tête -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary mb-3">
            Mes disponibilités
        </h1>
        <p class="lead text-muted">Gérez vos créneaux de consultation facilement</p>
    </div>

    <!-- CARTE AJOUT DE CRÉNEAU -->
    <div class="card shadow-lg border-0 mb-5">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0 fs-4">Ajouter un nouveau créneau</h5>
        </div>
        <div class="card-body p-4">
            <form id="add-availability-form" class="row g-4">
                @csrf
                <input type="hidden" name="action" value="add">

                <div class="col-md-5">
                    <label class="form-label fw-semibold text-dark">Heure de début</label>
                    <input type="datetime-local" name="start_time" class="form-control form-control-lg" required
                           min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                    <div class="form-text">Date et heure de début</div>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold text-dark">Heure de fin</label>
                    <input type="datetime-local" name="end_time" class="form-control form-control-lg" required
                           min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                    <div class="form-text">Date et heure de fin</div>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success btn-lg w-100 py-3">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- LISTE DES CRÉNEAUX -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light py-3">
            <h4 class="card-title mb-0 text-dark fs-4">Mes créneaux programmés</h4>
        </div>
        <div class="card-body p-4">
            @if($availabilities->count() > 0)
                <div class="row g-4">
                    @foreach($availabilities as $avail)
                        @php
                            $start = \Carbon\Carbon::parse($avail->start_time);
                            $end = \Carbon\Carbon::parse($avail->end_time);
                            $isPast = $start->isPast();
                            $isBooked = $avail->appointment;
                        @endphp

                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm 
                                {{ $isPast ? 'border-warning' : ($isBooked ? 'border-success' : 'border-primary') }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge {{ $isPast ? 'bg-warning' : ($isBooked ? 'bg-success' : 'bg-primary') }} fs-6">
                                            {{ $start->format('d/m/Y') }}
                                        </span>
                                        @if($isBooked)
                                            <span class="badge bg-success fs-7">Réservé</span>
                                        @elseif($isPast)
                                            <span class="badge bg-secondary fs-7">Passé</span>
                                        @else
                                            <span class="badge bg-info fs-7">Disponible</span>
                                        @endif
                                    </div>

                                    <div class="text-center mb-3">
                                        <p class="fs-4 fw-bold text-dark mb-1">{{ $start->format('H\hi') }}</p>
                                        <div class="text-muted">↓</div>
                                        <p class="fs-4 fw-bold text-dark mb-0">{{ $end->format('H\hi') }}</p>
                                    </div>

                                    <div class="small text-muted mb-3">
                                        <div>Durée: {{ $start->diffInMinutes($end) }} min</div>
                                        <div>{{ $start->translatedFormat('l') }}</div>
                                    </div>

                                    <div class="d-grid">
                                        @if(!$isPast && !$isBooked)
                                            <form class="delete-availability-form" data-id="{{ $avail->id }}">
                                                @csrf
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="availability_id" value="{{ $avail->id }}">
                                                <button type="submit" class="btn btn-outline-danger w-100">
                                                    Supprimer
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-outline-secondary w-100" disabled>
                                                @if($isBooked) Créneau réservé
                                                @else Créneau passé @endif
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $availabilities->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                    <h4 class="text-muted mb-3">Aucun créneau défini</h4>
                    <p class="text-muted">Ajoutez vos premiers créneaux avec le formulaire ci-dessus.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- SweetAlert2 + Script AJAX -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // === AJOUT DE CRÉNEAU ===
    document.getElementById('add-availability-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("{{ route('appointments.manage_availabilities') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Succès !',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message
                });
            }
        })
        .catch(() => {
            Swal.fire('Erreur', 'Problème de connexion au serveur', 'error');
        });
    });

    // === SUPPRESSION DE CRÉNEAU ===
    document.querySelectorAll('.delete-availability-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Confirmer la suppression ?',
                text: "Ce créneau sera définitivement supprimé",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = new FormData(this);

                    fetch("{{ route('appointments.manage_availabilities') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Supprimé !', data.message, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Impossible', data.message, 'error');
                        }
                    });
                }
            });
        });
    });
});
</script>

<!-- Styles (facultatifs, tu peux les garder dans app.css) -->
<style>
    .card { transition: all .2s ease; border-radius: 12px; }
    .card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,.15)!important; }
    .btn-success { background: linear-gradient(135deg, #28a745, #20c997); border: none; }
    .btn-success:hover { background: linear-gradient(135deg, #218838, #1ea085); }
</style>
@endsection
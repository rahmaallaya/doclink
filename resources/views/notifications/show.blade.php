{{-- resources/views/notifications/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Détail de la notification')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <div class="card shadow-lg border-0 overflow-hidden rounded-3">
                <div class="card-header text-white text-center py-4" 
                     style="background: linear-gradient(135deg, #007bff, #0056b3);">
                    <h4 class="mb-0">
                        <i class="fas fa-bell fa-beat-fade me-3"></i>
                        Détail de la notification
                    </h4>
                </div>

                <div class="card-body p-5">

                    <!-- Message complet -->
                    <div class="alert {{ $notification->read ? 'alert-light' : 'alert-primary' }} shadow-sm mb-5">
                        <p class="lead mb-0 lh-lg">
                            {!! nl2br(e($notification->message)) !!}
                        </p>
                    </div>

                    <hr class="my-5">

                    <!-- Infos -->
                    <div class="row text-muted small mb-5">
                        <div class="col-sm-6">
                            <strong><i class="far fa-calendar-alt me-2 text-primary"></i>Date :</strong><br>
                            <span class="fw-bold">{{ $notification->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <strong><i class="fas fa-eye me-2 text-primary"></i>Statut :</strong><br>
                            <span class="badge fs-6 {{ $notification->read ? 'bg-success' : 'bg-warning text-dark' }} px-4 py-2">
                                {{ $notification->read ? 'Lue' : 'Non lue' }}
                            </span>
                        </div>
                    </div>

                    <!-- Bouton d'action dynamique -->
                    @if($notification->related_id)
                        @php
                            $link = '#';
                            $btnText = 'Voir le détail';
                            $btnClass = 'btn-primary';
                            $icon = 'fas fa-external-link-alt';

                            switch(true) {
                                case str_contains($notification->type, 'appointment'):
                                    if($notification->appointment) {
                                        $link = route('appointments.show', $notification->appointment);
                                        $btnText = 'Voir le rendez-vous';
                                        $btnClass = 'btn-success';
                                        $icon = 'fas fa-calendar-check';
                                    }
                                    break;

                                case str_contains($notification->type, 'question') || str_contains($notification->type, 'forum'):
                                    $link = route('questions.show', $notification->related_id);
                                    $btnText = 'Voir la question';
                                    $btnClass = 'btn-info';
                                    $icon = 'fas fa-question-circle';
                                    break;

                                case str_contains($notification->type, 'private_message'):
                                    $link = route('messages.show', $notification->related_id);
                                    $btnText = 'Ouvrir la conversation';
                                    $btnClass = 'btn-purple';
                                    $icon = 'fas fa-envelope';
                                    break;
                            }
                        @endphp

                        <hr class="my-5">
                        <div class="text-center">
                            <p class="text-muted mb-4">Cette notification concerne une action dans l'application</p>
                            <a href="{{ $link }}" class="btn {{ $btnClass }} btn-lg px-5 shadow-lg">
                                <i class="{{ $icon }} me-2"></i>
                                {{ $btnText }}
                            </a>
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-light text-center border-0 py-4">
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary btn-lg px-5">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour aux notifications
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-purple {
        background: linear-gradient(135deg, #6f42c1, #5a2d91);
        border: none;
        color: white;
    }
    .btn-purple:hover {
        background: linear-gradient(135deg, #5a2d91, #45227a);
        color: white;
    }
    .transition-all { transition: all 0.3s ease; }
</style>
@endpush
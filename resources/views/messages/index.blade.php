{{-- resources/views/messages/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Messagerie privée')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-11">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary mb-0">
                <i class="fas fa-envelope me-3"></i> Messagerie privée
            </h2>

            @if(auth()->user()->role === 'medecin')
                <a href="{{ route('messages.create') }}" class="btn btn-primary shadow-sm px-4">
                    <i class="fas fa-edit"></i> Nouveau message
                </a>
            @elseif(auth()->user()->role === 'patient')
                <a href="{{ route('messages.create-patient') }}" class="btn btn-primary shadow-sm px-4">
                    <i class="fas fa-edit"></i> Contacter un médecin
                </a>
            @endif
        </div>

        @if($unreadCount > 0)
            <div class="alert alert-info rounded-4 shadow-sm border-0 mb-4">
                <i class="fas fa-bell"></i>
                Vous avez <strong>{{ $unreadCount }} message{{ $unreadCount > 1 ? 's' : '' }} non lu{{ $unreadCount > 1 ? 's' : '' }}</strong>
            </div>
        @endif

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                @forelse($conversations as $conv)
                    @php
                        $other = $conv->sender_id == auth()->id() ? $conv->receiver : $conv->sender;
                        $lastMessage = $conv;
                        $isUnread = !$lastMessage->read && $lastMessage->receiver_id == auth()->id();
                    @endphp

                    <a href="{{ route('messages.show', $other->id) }}"
                       class="d-block text-decoration-none text-dark border-bottom border-light hover-bg-primary-subtle transition-all px-4 py-4 {{ $isUnread ? 'bg-light' : '' }}"
                       style="transition: background 0.3s;">

                        <div class="d-flex align-items-center">
                            <!-- Avatar -->
                            <div class="position-relative me-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($other->name) }}&background={{ $other->role === 'medecin' ? '0d6efd' : '28a745' }}&color=fff&bold=true&size=80"
                                     alt="{{ $other->name }}"
                                     class="rounded-circle" width="60" height="60">
                                @if($isUnread)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        Nouveau
                                    </span>
                                @endif
                            </div>

                            <!-- Contenu -->
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h5 class="mb-1 fw-bold text-truncate">
                                        {{ $other->name }}
                                        @if($other->role === 'medecin')
                                            <small class="text-primary ms-2">
                                                <i class="fas fa-user-md"></i> Dr {{ $other->specialty ?? '' }}
                                            </small>
                                        @endif
                                    </h5>
                                    <small class="text-muted">
                                        {{ $lastMessage->created_at->diffForHumans() }}
                                    </small>
                                </div>

                                <p class="mb-0 text-muted text-truncate">
                                    {{ Str::limit($lastMessage->message, 80) }}
                                </p>
                            </div>

                            <!-- Icône flèche -->
                            <div class="ms-3 text-muted">
                                <i class="fas fa-chevron-right fa-lg"></i>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-envelope-open-text fa-4x text-muted mb-3"></i>
                        <p class="text-muted fs-5">Aucune conversation pour le moment</p>
                        <p class="text-muted">
                            @if(auth()->user()->role === 'patient')
                                Contactez votre médecin pour commencer
                            @else
                                Envoyez un message à un patient
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.hover-bg-primary-subtle:hover {
    background: rgba(0,123,255,0.05) !important;
}
.transition-all { transition: all 0.3s ease; }
</style>
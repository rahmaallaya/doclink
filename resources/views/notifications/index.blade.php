{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Mes Notifications')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            Notifications
            @if($unreadCount > 0)
                <span class="badge bg-danger rounded-pill fs-6">{{ $unreadCount }}</span>
            @endif
        </h1>

        {{-- ON NE MET PLUS DE BOUTON ICI --}}
        {{-- Tout est automatique dès qu’on arrive sur la page --}}
    </div>

    @if($notifications->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-bell-slash fa-4x text-muted mb-4"></i>
            <h4 class="text-muted">Aucune notification pour le moment</h4>
            <p class="text-muted">Vous serez averti dès qu’il y a du nouveau !</p>
        </div>
    @else
        <div class="list-group">
            @foreach($notifications as $notification)
                <a href="{{ route('notifications.show', $notification) }}" 
                   class="list-group-item list-group-item-action {{ $notification->read ? '' : 'bg-light fw-bold' }}">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">{{ Str::limit(strip_tags($notification->message), 80) }}</h6>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    @if(!$notification->read)
                        <small class="text-primary"><i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> Non lue</small>
                    @endif
                </a>
            @endforeach
        </div>

        {{ $notifications->links() }}
    @endif
</div>
@endsection
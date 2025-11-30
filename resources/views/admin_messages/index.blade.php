{{-- resources/views/admin_messages/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Support & Messages')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-3">
        <div>
            <h2 class="fw-bold text-primary mb-2">
                <i class="fas fa-headset me-3"></i> Centre de Support
            </h2>
            <p class="text-muted mb-0">Suivez vos demandes et les réponses de l'équipe</p>
        </div>
        <a href="{{ route('admin_messages.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-plus-circle"></i> Nouveau message
        </a>
    </div>

    @forelse($messages as $msg)
        @php
            $isAdmin = auth()->user()->role === 'admin';
            $isOwner = $msg->user_id === auth()->id();
            $canEdit = $isOwner && $msg->status !== 'resolved';
            $canDeleteTicket = $isOwner;                    // SEULEMENT le créateur
            $hasResponse = !is_null($msg->admin_response);
        @endphp

        <div class="card shadow-sm border-0 rounded-4 mb-4 {{ $msg->status === 'resolved' ? 'opacity-75 border-success' : '' }}">
            <div class="card-header bg-white border-0 py-4">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <h5 class="mb-2 fw-bold">{{ $msg->subject }}</h5>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="badge bg-{{ $msg->priority === 'high' ? 'danger' : ($msg->priority === 'medium' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($msg->priority) }}
                            </span>
                            <span class="badge bg-{{ $msg->status === 'resolved' ? 'success' : ($msg->status === 'in_progress' ? 'info' : 'light text-dark') }}">
                                {{ $msg->status === 'open' ? 'En attente' : ($msg->status === 'in_progress' ? 'En cours' : 'Résolu') }}
                            </span>
                            <small class="text-muted">
                                Par {{ $msg->user->name }} • {{ $msg->created_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                    </div>

                    @if($isAdmin)
                        <form action="{{ route('admin_messages.updateStatus', $msg->id) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="open" {{ $msg->status === 'open' ? 'selected' : '' }}>En attente</option>
                                <option value="in_progress" {{ $msg->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                                <option value="resolved" {{ $msg->status === 'resolved' ? 'selected' : '' }}>Résolu</option>
                            </select>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <div class="bg-light bg-opacity-50 rounded-4 p-4 mb-4">
                    <p class="mb-0">{{ $msg->message }}</p>
                </div>

                @if($hasResponse)
                    <div class="bg-primary bg-opacity-10 rounded-4 p-4 border-start border-primary border-5">
                        <p class="fw-bold text-primary mb-2">
                            <i class="fas fa-user-shield"></i> Réponse de l'équipe DocLink
                        </p>
                        <p class="mb-0">{{ $msg->admin_response }}</p>
                    </div>
                @elseif($msg->admin_response === null && $msg->status !== 'open')
                    <div class="bg-light rounded-4 p-4 border-start border-secondary border-4 opacity-75">
                        <em class="text-muted">La réponse précédente a été supprimée par l'administrateur</em>
                    </div>
                @endif
            </div>

            <div class="card-footer bg-white border-0 d-flex flex-wrap gap-2 py-3">
                <!-- Admin : Répondre / Modifier / Supprimer réponse -->
                @if($isAdmin && !$hasResponse)
                    <a href="{{ route('admin_messages.editResponse', $msg->id) }}" class="btn btn-outline-primary btn-sm">
                        Répondre
                    </a>
                @endif

                @if($isAdmin && $hasResponse)
                    <a href="{{ route('admin_messages.editResponse', $msg->id) }}" class="btn btn-outline-warning btn-sm">
                        Modifier réponse
                    </a>

                    <form action="{{ route('admin_messages.destroyResponse', $msg->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Supprimer votre réponse uniquement ?')">
                            Supprimer ma réponse
                        </button>
                    </form>
                @endif

                <!-- Créateur du ticket : Modifier + Supprimer tout le ticket -->
                @if($canEdit)
                    <a href="{{ route('admin_messages.edit', $msg->id) }}" class="btn btn-outline-secondary btn-sm">
                        Modifier
                    </a>
                @endif

                @if($canDeleteTicket)
                    <form action="{{ route('admin_messages.destroy', $msg->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Supprimer définitivement TOUT votre message ? Cette action est irréversible.')">
                            Supprimer le ticket
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-envelope-open-text fa-5x text-muted mb-4"></i>
            <h4 class="text-muted">Aucun message</h4>
            <p class="text-muted">Votre boîte de support est vide.</p>
        </div>
    @endforelse
</div>
@endsection
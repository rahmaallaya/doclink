{{-- resources/views/messages/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Conversation avec ' . $otherUser->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-10">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <!-- Header -->
            <div class="card-header bg-primary text-white d-flex align-items-center py-3 px-4">
                <a href="{{ route('messages.index') }}" class="text-white me-4">
                    <i class="fas fa-arrow-left fa-lg"></i>
                </a>
                <img src="https://ui-avatars.com/api/?name={{ urlencode($otherUser->name) }}&background=007bff&color=fff&size=80"
                     alt="{{ $otherUser->name }}" class="rounded-circle me-3" width="48">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $otherUser->name }}</h5>
                    <small class="opacity-75">
                        @if($otherUser->role === 'medecin')
                            Dr {{ $otherUser->specialty }}
                        @else
                            Patient
                        @endif
                    </small>
                </div>
            </div>

            <!-- Messages -->
            <div class="card-body p-4" style="max-height: 65vh; overflow-y: auto; background: #f0f4f8;" id="messages-container">
                @forelse($messages as $msg)
                    <div class="d-flex mb-4 {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="{{ $msg->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-white border shadow-sm' }} 
                                     rounded-4 px-4 py-3 position-relative" style="max-width: 75%;">
                            
                            <p class="mb-2">{{ nl2br(e($msg->message)) }}</p>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="{{ $msg->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                    {{ $msg->created_at->format('H:i') }}
                                </small>

                                @if($msg->sender_id == auth()->id())
                                    <div class="dropdown d-inline">
                                        <a href="#" class="text-white-50 text-decoration-none" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v fa-sm"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                            <li>
                                                <a href="{{ route('messages.edit', $msg->id) }}" 
                                                   class="dropdown-item text-warning">
                                                    <i class="fas fa-edit me-2"></i> Modifier
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('messages.destroy', $msg->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('Supprimer ce message ?')">
                                                        <i class="fas fa-trash me-2"></i> Supprimer
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Statut lu -->
                                    @if($msg->read)
                                        <i class="fas fa-check-double text-white ms-2"></i>
                                    @else
                                        <i class="fas fa-check text-white-50 ms-2"></i>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>Aucun message · Commencez la conversation !</p>
                    </div>
                @endforelse
            </div>

            <!-- Formulaire d'envoi -->
            <div class="card-footer bg-white border-0 p-4">
                <form action="{{ route('messages.send', $otherUser->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <textarea name="message" class="form-control border-0 shadow-sm" rows="2"
                                  placeholder="Tapez votre message..." required autofocus></textarea>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Scroll auto en bas
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    });
</script>
@endsection
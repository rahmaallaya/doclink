{{-- resources/views/admin_messages/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nouveau message - Support')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-headset me-3"></i>
                        Contacter le Support DocLink
                    </h3>
                </div>

                <div class="card-body p-5">
                    <form action="{{ route('admin_messages.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="subject" class="form-label fw-bold text-primary">Sujet</label>
                            <input type="text" name="subject" id="subject" class="form-control form-control-lg shadow-sm @error('subject') is-invalid @enderror"
                                   value="{{ old('subject') }}" placeholder="Ex: Problème de connexion" required>
                            @error('subject')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label fw-bold text-primary">Votre message</label>
                            <textarea name="message" id="message" rows="7" class="form-control shadow-sm @error('message') is-invalid @enderror"
                                      placeholder="Décrivez votre problème ou votre demande..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="priority" class="form-label fw-bold text-primary">Priorité</label>
                            <select name="priority" id="priority" class="form-select form-select-lg shadow-sm">
                                <option value="low">Basse – Information générale</option>
                                <option value="medium" selected>Moyenne – Problème modéré</option>
                                <option value="high">Haute – Bloquant / Urgent</option>
                            </select>
                        </div>

                        @if(auth()->user()->role === 'admin')
                            <div class="mb-4 border-top pt-4">
                                <label for="user_id" class="form-label fw-bold text-danger">Envoyer en tant qu'Admin</label>
                                <select name="user_id" id="user_id" class="form-select form-select-lg shadow-sm">
                                    <option value="">-- Choisir un destinataire --</option>
                                    <option value="all" class="text-primary fw-bold">Tous les utilisateurs</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} — {{ ucfirst($user->role) }}
                                            @if($user->role === 'medecin') • Dr {{ $user->specialty }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <a href="{{ route('admin_messages.index') }}" class="btn btn-outline-secondary btn-lg px-5">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-paper-plane"></i> Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- resources/views/admin_messages/edit_response.blade.php --}}
@extends('layouts.app')
@section('title', 'Répondre au message')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4><i class="fas fa-reply"></i> Répondre au message de support</h4>
                </div>
                <div class="card-body p-5">
                    <div class="bg-light rounded-4 p-4 mb-5 border-start border-primary border-5">
                        <p><strong>De :</strong> {{ $message->user->name }} ({{ ucfirst($message->user->role) }})</p>
                        <p><strong>Sujet :</strong> {{ $message->subject }}</p>
                        <p><strong>Message :</strong></p>
                        <p class="mb-0">{{ $message->message }}</p>
                    </div>

                    <form action="{{ route('admin_messages.updateResponse', $message->id) }}" method="POST">
                        @csrf @method('PATCH')

                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary">Votre réponse</label>
                            <textarea name="admin_response" class="form-control form-control-lg" rows="8" required>{{ old('admin_response', $message->admin_response) }}</textarea>
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox" name="mark_resolved" class="form-check-input" id="resolve">
                            <label class="form-check-label fw-bold" for="resolve">
                                Marquer ce ticket comme résolu
                            </label>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="{{ route('admin_messages.index') }}" class="btn btn-outline-secondary btn-lg">Retour</a>
                            <button type="submit" class="btn btn-success btn-lg px-5">Envoyer la réponse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
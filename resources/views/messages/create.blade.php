{{-- resources/views/messages/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nouveau message - Médecin')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white text-center py-4 rounded-top-4">
                <h3 class="mb-0"><i class="fas fa-paper-plane me-3"></i> Envoyer un message à un patient</h3>
            </div>

            <div class="card-body p-5">
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Patient destinataire</label>
                        <select name="receiver_id" class="form-select form-select-lg shadow-sm" required>
                            <option value="">Sélectionner un patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Sujet (optionnel)</label>
                        <input type="text" name="subject" class="form-control form-control-lg shadow-sm" placeholder="Ex: Résultats d'analyses...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Message</label>
                        <textarea name="message" rows="6" class="form-control shadow-sm" placeholder="Votre message sécurisé..." required></textarea>
                    </div>

                    <div class="d-grid d-md-flex justify-content-end gap-3">
                        <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary btn-lg px-5">Annuler</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-paper-plane"></i> Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
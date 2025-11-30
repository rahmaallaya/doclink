{{-- resources/views/messages/create_patient.blade.php --}}
@extends('layouts.app')
@section('title', 'Contacter un médecin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient text-white text-center py-4"
                     style="background: linear-gradient(135deg, #007bff, #6610f2);">
                    <h3 class="mb-0">
                        <i class="fas fa-user-md me-3"></i> Envoyer un message à un médecin
                    </h3>
                </div>

                <div class="card-body p-5">
                    <form action="{{ route('messages.store-patient') }}" method="POST">
                        @csrf

                        <!-- Sélection du médecin (pré-sélectionné si doctor_id dans l'URL) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary">
                                <i class="fas fa-user-md me-2"></i> Choisir votre médecin
                            </label>
                            <select name="receiver_id" class="form-select form-select-lg shadow-sm" required>
                                <option value="">-- Sélectionner un médecin --</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr {{ $doctor->name }} — {{ $doctor->specialty ?? 'Généraliste' }}
                                        @if($doctor->location) ({{ $doctor->location }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Sujet (optionnel)</label>
                            <input type="text" name="subject" class="form-control form-control-lg shadow-sm"
                                   placeholder="Ex: Question sur mes résultats...">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Votre message</label>
                            <textarea name="message" rows="7" class="form-control shadow-sm"
                                      placeholder="Décrivez votre demande en détail..." required></textarea>
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-end gap-3 mt-5">
                            <a href="{{ route('appointments.search') }}" 
                               class="btn btn-outline-secondary btn-lg px-5 order-md-1">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-success btn-lg px-5 shadow">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-6">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-gradient text-white text-center py-5" style="background: linear-gradient(135deg, #007bff, #0056b3);">
                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=007bff&color=fff&size=128' }}"
                     alt="Avatar" class="rounded-circle mb-3" width="120" height="120" style="border: 5px solid rgba(255,255,255,0.3);">
                <h3 class="mb-0">{{ auth()->user()->name }}</h3>
                <p class="mb-0 opacity-75">{{ ucfirst(auth()->user()->role) }}
                    @if(auth()->user()->role === 'medecin') • Dr {{ auth()->user()->specialty }} @endif
                </p>
            </div>

            <div class="card-body p-5">
                @if(session('success'))
                    <div class="alert alert-success rounded-4 shadow-sm">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4 text-center">
                        <label class="btn btn-outline-primary rounded-circle position-relative overflow-hidden d-inline-block" style="width: 120px; height: 120px;">
                            <i class="fas fa-camera fa-3x"></i>
                            <input type="file" name="avatar" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer">
                        </label>
                        <small class="d-block text-muted mt-2">Cliquez pour changer l'avatar</small>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nom complet</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                   class="form-control form-control-lg shadow-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                   class="form-control form-control-lg shadow-sm" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Téléphone (optionnel)</label>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                   class="form-control form-control-lg shadow-sm" placeholder="+33 6 12 34 56 78">
                        </div>

                        @if(auth()->user()->role === 'medecin')
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Spécialité</label>
                                <input type="text" class="form-control form-control-lg bg-light" value="{{ auth()->user()->specialty }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Lieu d'exercice</label>
                                <input type="text" class="form-control form-control-lg bg-light" value="{{ auth()->user()->location }}" disabled>
                            </div>
                        @endif
                    </div>

                    <div class="d-grid d-md-flex justify-content-end gap-3 mt-5">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-lg px-5">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')
@section('title', 'Inscription Patient')

@section('content')
<div class="container" style="max-width: 520px; margin-top: 80px;">
    <div class="card shadow-lg">
        <div class="card-body p-5">
            <h2 class="text-center mb-4">Inscription Patient</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('register.patient') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nom complet</label>
                    <input type="text" name="name" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-lg" required>
                </div>
                <button class="btn btn-success btn-lg w-100">Créer mon compte patient</button>
            </form>

            <p class="text-center mt-4">
                Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a>
            </p>
        </div>
    </div>
</div>
@endsection
{{-- resources/views/questions/index.blade.php --}}
{{-- VERSION CORRIGÉE & ULTRA PROPRE – PLUS JAMAIS D’ERREUR $role --}}
@extends('layouts.app')
@section('title', 'Forum Médical - Toutes les questions')

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h2 fw-bold text-primary mb-1">Forum Médical</h1>
            <p class="text-muted">Posez vos questions ou aidez les patients</p>
        </div>

        @if(auth()->user()->role === 'patient')
            <a href="{{ route('questions.create') }}" class="btn btn-primary btn-lg shadow">
                Poser une question
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($questions as $question)
            <div class="col-12">
                <div class="card shadow-sm border-start border-primary border-4 h-100">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-2">
                                    <a href="{{ route('questions.show', $question) }}" class="text-decoration-none text-dark stretched-link">
                                        {{ $question->title }}
                                    </a>
                                </h5>
                                <p class="text-muted mb-3">{{ Str::limit(strip_tags($question->content), 180) }}</p>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-3 text-sm">
                            <div>
                                <span class="badge bg-info fs-6">{{ $question->category->name }}</span>
                                <span class="badge bg-{{ $question->status === 'open' ? 'warning' : 'success' }} fs-6">
                                    {{ $question->status === 'open' ? 'En attente' : 'Répondue' }}
                                </span>
                                <span class="badge bg-secondary fs-6">
                                    {{ $question->answers->count() }} réponse{{ $question->answers->count() > 1 ? 's' : '' }}
                                </span>
                            </div>

                            <div class="ms-auto text-muted small">
                                par <strong>{{ $question->patient->name }}</strong>
                                • {{ $question->created_at->diffForHumans() }}
                            </div>
                        </div>

                        {{-- Boutons pour le patient qui a posé la question --}}
                        @if(auth()->id() === $question->patient_id)
                            <div class="mt-3 text-end">
                                <a href="{{ route('questions.edit', $question) }}" class="btn btn-sm btn-outline-warning">
                                    Modifier
                                </a>
                                <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Supprimer définitivement cette question ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Bouton "Répondre" pour le médecin de la bonne spécialité --}}
                        @if(auth()->user()->role === 'medecin' && auth()->user()->specialty === $question->category->name)
                            <div class="mt-3 text-end">
                                <a href="{{ route('questions.show', $question) }}" class="btn btn-success btn-sm">
                                    Répondre à cette question
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-light rounded">
                    <i class="fas fa-comments fa-5x text-muted mb-4"></i>
                    <h3 class="text-muted">Aucune question pour le moment</h3>
                    @if(auth()->user()->role === 'patient')
                        <p class="lead">Soyez le premier à poser une question !</p>
                        <a href="{{ route('questions.create') }}" class="btn btn-primary btn-lg">Poser une question</a>
                    @else
                        <p class="lead">Revenez plus tard pour aider les patients</p>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $questions->links() }}
    </div>
</div>
@endsection
{{-- resources/views/questions/my_questions.blade.php --}}
@extends('layouts.app')
@section('title', 'Mes questions')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mes questions</h1>
        <div>
            <a href="{{ route('questions.create') }}" class="btn btn-primary me-2">
                Nouvelle question
            </a>
            <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">
                Voir le forum
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($questions as $q)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <a href="{{ route('questions.show', $q) }}" class="text-decoration-none">
                                {{ Str::limit($q->title, 50) }}
                            </a>
                        </h5>
                        <p class="text-muted small flex-grow-1">{{ Str::limit($q->content, 100) }}</p>
                        <div class="mt-auto">
                            <span class="badge bg-info mb-2">{{ $q->category->name }}</span>
                            <div class="text-muted small">
                                {{ $q->answers->count() }} réponse{{ $q->answers->count() > 1 ? 's' : '' }}
                                • {{ $q->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="{{ route('questions.edit', $q) }}" class="btn btn-sm btn-outline-warning">Modifier</a>
                        <form action="{{ route('questions.destroy', $q) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                <h4>Vous n'avez posé aucune question</h4>
                <a href="{{ route('questions.create') }}" class="btn btn-primary btn-lg mt-3">
                    Poser votre première question
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
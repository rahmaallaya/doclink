@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <!-- Question -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $question->title }}</h5>
                    <div>
                        <span class="badge bg-light text-dark">{{ $question->category->name }}</span>
                        @if($question->status === 'open')
                            <span class="badge bg-warning">En attente</span>
                        @else
                            <span class="badge bg-success">Répondue</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Posée par <strong>{{ $question->patient->name }}</strong> 
                        il y a {{ $question->created_at->diffForHumans() }}
                    </p>
                    <p class="lead">{!! nl2br(e($question->content)) !!}</p>

                    @if($question->patient_id === auth()->id())
                        <div class="text-end mt-3">
                            <a href="{{ route('questions.edit', $question) }}" class="btn btn-warning btn-sm">Modifier</a>
                            <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Supprimer cette question ?')">Supprimer</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Réponses -->
            <h4 class="mt-4">
                Réponses <span class="badge bg-primary">{{ $question->answers->count() }}</span>
            </h4>

            @if($question->answers->count() === 0)
                <div class="card text-center py-5 bg-light">
                    <p class="text-muted">Aucune réponse pour le moment</p>
                </div>
            @else
                @foreach($question->answers as $answer)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $answer->doctor->name }}</strong>
                                    <small class="text-muted">(Médecin {{ $answer->doctor->specialty }})</small>
                                    <small class="text-muted ms-3">{{ $answer->created_at->diffForHumans() }}</small>
                                </div>

                                <!-- BOUTONS MODIFIER / SUPPRIMER (seulement pour l'auteur) -->
                                @if(auth()->id() === $answer->doctor_id)
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('questions.answers.edit', $answer) }}" class="btn btn-outline-warning btn-sm">
                                            Modifier
                                        </a>
                                       <form action="{{ route('questions.answers.destroy', $answer) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Supprimer définitivement votre réponse ?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3 p-3 bg-light rounded">
                                {!! nl2br(e($answer->content)) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Formulaire réponse médecin -->
            @if(Auth::user()->role === 'medecin' && Auth::user()->specialty === $question->category->name)
                <div class="card mt-5 border-success shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Votre réponse professionnelle</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('questions.answer', $question) }}" method="POST">
                            @csrf
                            <textarea name="content" class="form-control" rows="8" 
                                      placeholder="Rédigez une réponse claire et bienveillante..." required></textarea>
                            @error('content')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    Publier la réponse
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('questions.index') }}" class="btn btn-outline-primary btn-lg">Retour au forum</a>
            </div>
        </div>
    </div>
</div>
@endsection
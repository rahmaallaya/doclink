@extends('layouts.app')

@section('title', 'Modifier ma réponse')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h3>Modifier votre réponse</h3>
                </div>
                <div class="card-body p-5">

                    <div class="alert alert-info mb-4">
                        <strong>Question :</strong><br>
                        <h5 class="mt-2">{{ $answer->question->title }}</h5>
                    </div>

                   <form action="{{ route('questions.answers.update', $answer) }}" method="POST">
                        @csrf @method('PUT')

                        <textarea name="content" class="form-control" rows="10" required>{{ old('content', $answer->content) }}</textarea>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-warning btn-lg px-5">
                                Enregistrer
                            </button>
                            <a href="{{ route('questions.show', $answer->question) }}" class="btn btn-secondary btn-lg">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
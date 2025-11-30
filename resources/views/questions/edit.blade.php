@extends('layouts.app')

@section('title', 'Modifier ma question')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-dark">
                    <h3 class="mb-0">Modifier votre question</h3>
                </div>
                <div class="card-body p-5">

                    <!-- LE FORMULAIRE CORRIGÉ AVEC @csrf ET @method('PUT') -->
                    <form action="{{ route('questions.update', $question) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold">Catégorie</label>
                            <select name="category_id" class="form-select form-select-lg" required>
                                <option value="">-- Choisir une spécialité --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                        {{ $question->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Titre</label>
                            <input type="text" name="title" class="form-control form-control-lg" 
                                   value="{{ old('title', $question->title) }}" required>
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Description complète</label>
                            <textarea name="content" class="form-control" rows="12" required>{{ old('content', $question->content) }}</textarea>
                            @error('content')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="d-flex gap-3 justify-content-end">
                            <button type="submit" class="btn btn-warning btn-lg px-5">
                                Enregistrer
                            </button>
                            <a href="{{ route('questions.show', $question) }}" 
                               class="btn btn-secondary btn-lg px-5">
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
{{-- resources/views/questions/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Poser une question')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Poser une question au forum médical</h3>
                </div>
                <div class="card-body p-5">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('questions.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Catégorie de votre problème</label>
                            <select name="category_id" class="form-select form-select-lg" required>
                                <option value="">-- Choisissez une spécialité --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Titre clair et précis</label>
                            <input type="text" name="title" class="form-control form-control-lg"
                                   value="{{ old('title') }}" placeholder="Ex: Douleurs abdominales depuis 3 jours..." required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Décrivez votre problème en détail</label>
                            <textarea name="content" class="form-control" rows="10" required
                                      placeholder="Décrivez vos symptômes, depuis quand, ce que vous avez déjà essayé...">{{ old('content') }}</textarea>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                Publier la question
                            </button>
                            <a href="{{ route('questions.index') }}" class="btn btn-secondary btn-lg">
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
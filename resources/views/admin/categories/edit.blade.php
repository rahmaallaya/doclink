@extends('layouts.app')
@section('title', 'Modifier une Catégorie')

@section('content')
<div class="container py-5">
    <h1>Modifier la catégorie : {{ $category->name }}</h1>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nom de la catégorie</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Description (optionnelle)</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-warning">Mettre à jour</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
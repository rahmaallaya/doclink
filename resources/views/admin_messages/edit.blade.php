{{-- resources/views/admin_messages/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Modifier le message')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-warning text-dark text-center py-4">
                    <h4><i class="fas fa-edit"></i> Modifier votre demande</h4>
                </div>
                <div class="card-body p-5">
                    <form action="{{ route('admin_messages.update', $message->id) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold">Sujet</label>
                            <input type="text" name="subject" class="form-control form-control-lg" 
                                   value="{{ old('subject', $message->subject) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Message</label>
                            <textarea name="message" class="form-control" rows="6" required>{{ old('message', $message->message) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Priorité</label>
                            <select name="priority" class="form-select form-select-lg">
                                <option value="low" {{ $message->priority === 'low' ? 'selected' : '' }}>Basse</option>
                                <option value="medium" {{ $message->priority === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                <option value="high" {{ $message->priority === 'high' ? 'selected' : '' }}>Haute</option>
                            </select>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="{{ route('admin_messages.index') }}" class="btn btn-outline-secondary btn-lg px-5">Annuler</a>
                            <button type="submit" class="btn btn-warning text-dark btn-lg px-5">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
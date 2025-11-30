{{-- resources/views/messages/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Modifier le message')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-warning text-dark text-center py-4">
                <h4><i class="fas fa-edit"></i> Modifier votre message</h4>
            </div>
            <div class="card-body p-5">
                <form action="{{ route('messages.update', $msg->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">Message</label>
                        <textarea name="message" class="form-control form-control-lg shadow-sm" rows="6" required>{{ old('message', $msg->message) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-lg">Annuler</a>
                        <button type="submit" class="btn btn-warning btn-lg text-dark">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
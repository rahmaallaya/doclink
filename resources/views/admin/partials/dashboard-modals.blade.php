{{-- resources/views/admin/partials/dashboard-modals.blade.php --}}
{{-- VERSION CORRIGÉE – PLUS DE MEMORY EXHAUSTED --}}

{{-- 1. Modale Patients --}}
<div class="modal fade" id="patientsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5>Patients ({{ $stats['total_patients'] }})</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead><tr><th>Nom</th><th>Email</th><th>Inscrit le</th></tr></thead>
                    <tbody>
                        @foreach($patients as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->email }}</td>
                            <td>{{ $p->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 2. Modale Médecins actifs --}}
<div class="modal fade" id="doctorsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5>Médecins actifs ({{ $stats['total_medecins'] }})</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead><tr><th>Nom</th><th>Spécialité</th><th>Ville</th><th>Inscrit le</th></tr></thead>
                    <tbody>
                        @foreach($doctors as $d)
                        <tr>
                            <td>Dr. {{ $d->name }}</td>
                            <td>{{ $d->specialty ?? '—' }}</td>
                            <td>{{ $d->location ?? '—' }}</td>
                            <td>{{ $d->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- 3. Modale En attente (liste) --}}
<div class="modal fade" id="pendingModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5>Médecins en attente de validation ({{ $stats['pending_medecins'] }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @if($pendingDoctors->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pendingDoctors as $doc)
                        <div class="list-group-item p-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-4" style="width:70px;height:70px;">
                                        <i class="bi bi-person-fill text-muted fs-2"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Dr. {{ $doc->name }}</h5>
                                        <p class="mb-1 text-muted">
                                            {{ $doc->specialty ?? 'Non renseignée' }}
                                            @if($doc->location) • {{ $doc->location }} @endif
                                        </p>
                                        <small class="text-muted">{{ $doc->email }}</small>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#doctorDetail-{{ $doc->id }}">
                                        Voir détails
                                    </button>
                                    <form action="{{ route('admin.approve', $doc->id) }}" method="POST" class="d-inline">@csrf
                                        <button class="btn btn-success btn-sm" onclick="return confirm('Valider ?')">Valider</button>
                                    </form>
                                    <form action="{{ route('admin.reject', $doc->id) }}" method="POST" class="d-inline ms-2">@csrf
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Rejeter ?')">Rejeter</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-5 text-center text-success">
                        <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
                        <p class="mt-3 fs-4">Tous les médecins sont validés !</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- 4. TOUTES les modales de détail – hors de la boucle ! --}}
@foreach($pendingDoctors as $doc)
<div class="modal fade" id="doctorDetail-{{ $doc->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5>Détails - Dr. {{ $doc->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="my-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:130px;height:130px;">
                        <i class="bi bi-person-fill text-muted fs-1"></i>
                    </div>
                    <h4>Dr. {{ $doc->name }}</h4>
                </div>
                <table class="table table-bordered">
                    <tr><th>Email</th><td>{{ $doc->email }}</td></tr>
                    <tr><th>Spécialité</th><td>{{ $doc->specialty ?? 'Non renseignée' }}</td></tr>
                    <tr><th>Ville</th><td>{{ $doc->location ?? 'Non renseignée' }}</td></tr>
                    <tr><th>Inscription</th><td>{{ $doc->created_at->format('d/m/Y à H:i') }}</td></tr>
                </table>
                <div class="alert alert-info">
                    Profil incomplet (photo, téléphone, bio, documents manquants).
                </div>
            </div>
            <div class="modal-footer">
                <form action="{{ route('admin.approve', $doc->id) }}" method="POST" class="d-inline">@csrf
                    <button class="btn btn-success btn-lg">Valider ce médecin</button>
                </form>
                <form action="{{ route('admin.reject', $doc->id) }}" method="POST" class="d-inline ms-3">@csrf
                    <button class="btn btn-danger btn-lg" onclick="return confirm('Rejeter définitivement ?')">Rejeter</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- 5. Modale Rendez-vous --}}
<div class="modal fade" id="appointmentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5>Tous les rendez-vous ({{ $stats['total_rdv'] }})</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr><th>Patient</th><th>Médecin</th><th>Date</th><th>Statut</th></tr>
                        </thead>
                        <tbody>
                            @foreach($allAppointments as $a)
                            <tr>
                                <td>{{ $a->user->name }}</td>
                                <td>Dr. {{ $a->doctor->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($a->appointment_time)->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-{{ $a->status === 'confirmed' ? 'success' : 'secondary' }}">{{ ucfirst($a->status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- resources/views/doctors/profile.blade.php --}}
@extends('layouts.app')
@section('title', 'Dr. ' . $doctor->name)

@section('content')
<div class="container py-5">

    <!-- Bouton retour -->
    <div class="mb-4">
        <a href="{{ route('appointments.search') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Retour à la recherche
        </a>
    </div>

    <div class="row g-5">
        <!-- === COLONNE GAUCHE : Profil + Google Maps === -->
        <div class="col-lg-8">

            <!-- Profil du médecin -->
            <div class="card shadow-lg border-0 rounded-4 mb-5 overflow-hidden">
                <div class="card-body p-5">
                    <div class="d-flex align-items-start gap-4 flex-wrap">
                        <img src="{{ $doctor->avatar }}"
                             alt="Dr. {{ $doctor->name }}"
                             class="rounded-circle border border-4 border-primary shadow"
                             style="width: 140px; height: 140px; object-fit: cover;">

                        <div class="flex-grow-1">
                            <h1 class="h2 fw-bold text-primary mb-1">
                                Dr. {{ $doctor->name }}
                                <i class="fas fa-check-circle text-success ms-2" title="Médecin vérifié"></i>
                            </h1>
                            <h5 class="text-muted mb-3">
                                <i class="fas fa-stethoscope me-2"></i>
                                {{ $doctor->specialty ?? 'Médecin généraliste' }}
                            </h5>

                            <div class="d-flex flex-wrap gap-3 mb-4">
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="fas fa-map-marker-alt text-danger"></i> {{ $doctor->location }}
                                </span>
                                @if($doctor->phone)
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="fas fa-phone text-success"></i> {{ $doctor->phone }}
                                </span>
                                @endif
                            </div>

                            <!-- Boutons d'action -->
                            @auth
                                @if(auth()->user()->role === 'patient')
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="{{ route('appointments.availabilities', $doctor->id) }}"
                                       class="btn btn-danger btn-lg shadow">
                                        <i class="fas fa-calendar-check me-2"></i> Prendre rendez-vous
                                    </a>
                                    <a href="{{ route('messages.create-patient') }}?doctor_id={{ $doctor->id }}"
                                       class="btn btn-info btn-lg shadow">
                                        <i class="fas fa-comments me-2"></i> Contacter le médecin
                                    </a>
                                </div>
                                @endif
                            @endauth
                        </div>
                    </div>

                    @if($doctor->bio)
                    <hr class="my-5">
                    <h5 class="fw-bold text-primary mb-3">À propos du médecin</h5>
                    <p class="text-muted lh-lg">{{ $doctor->bio }}</p>
                    @endif
                </div>
            </div>

            <!-- === GOOGLE MAPS – Toujours affiché === -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Localisation du cabinet médical
                    </h5>
                </div>

                <!-- La carte -->
                <div id="doctor-map" style="height: 500px; width: 100%;"></div>

                <!-- Bouton itinéraire + fallback -->
                <div class="card-footer bg-light text-center py-3">
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($doctor->location) }}"
                       target="_blank"
                       class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-directions me-2"></i> Obtenir l'itinéraire
                    </a>
                </div>
            </div>
        </div>

        <!-- === COLONNE DROITE : Actions rapides + Disponibilités === -->
        <div class="col-lg-4">

            <!-- Actions rapides (patient uniquement) -->
            @auth
                @if(auth()->user()->role === 'patient')
                <div class="card shadow border-0 rounded-4 text-white mb-4"
                     style="background: linear-gradient(135deg, #007bff, #6610f2);">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-headset fa-4x mb-4"></i>
                        <h5 class="fw-bold mb-4">Prendre contact rapidement</h5>
                        <a href="{{ route('appointments.availabilities', $doctor->id) }}"
                           class="btn btn-light btn-lg w-100 mb-3 shadow">
                            <i class="fas fa-calendar-plus me-2"></i> Prendre RDV
                        </a>
                        <a href="{{ route('messages.create-patient') }}?doctor_id={{ $doctor->id }}"
                           class="btn btn-outline-light btn-lg w-100 shadow">
                            <i class="fas fa-envelope me-2"></i> Envoyer un message
                        </a>
                    </div>
                </div>
                @endif
            @endauth

            <!-- Prochaines disponibilités -->
            @if($doctor->availabilities->count() > 0)
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i> Prochaines disponibilités
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($doctor->availabilities->take(6) as $slot)
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-primary">
                                        {{ $slot->start_time->translatedFormat('l d F Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $slot->start_time->format('H\hi') }} → {{ $slot->end_time->format('H\hi') }}
                                    </small>
                                </div>
                                <span class="badge bg-success rounded-pill px-3">Libre</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('appointments.availabilities', $doctor->id) }}"
                           class="btn btn-outline-success w-100">
                            Voir tous les créneaux
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow border-0 rounded-4 text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <p class="text-muted">Aucune disponibilité pour le moment</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- =================================== GOOGLE MAPS SCRIPT =================================== --}}
<script>
// Données à passer à JavaScript (échappées proprement)
window.doctorLocation = @json($doctor->location);
window.doctorName     = @json("Dr. " . $doctor->name);
</script>

{{-- Chargement de l'API Google Maps --}}
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initDoctorMap&libraries=places&language=fr®ion=TN" async defer></script>

<script>
function initDoctorMap() {
    const address = window.doctorLocation;
    const doctorName = window.doctorName;

    const geocoder = new google.maps.Geocoder();

    geocoder.geocode({ address: address, region: 'TN' }, (results, status) => {
        const mapDiv = document.getElementById('doctor-map');

        if (status === 'OK' && results[0]) {
            const location = results[0].geometry.location;

            const map = new google.maps.Map(mapDiv, {
                zoom: 16,
                center: location,
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: true,
                styles: [
                    { featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] }
                ]
            });

            const marker = new google.maps.Marker({
                map: map,
                position: location,
                title: doctorName,
                animation: google.maps.Animation.BOUNCE,
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(50, 50)
                }
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="padding:10px; max-width:280px;">
                        <h6 class="fw-bold text-primary mb-1">${doctorName}</h6>
                        <p class="mb-0 text-muted"><i class="fas fa-map-marker-alt text-danger"></i> ${address}</p>
                    </div>
                `
            });

            infoWindow.open(map, marker);
            marker.addListener('click', () => infoWindow.open(map, marker));

        } else {
            // === AFFICHAGE DE SECOURS SI LA CARTE NE CHARGE PAS ===
            console.warn("Geocoding échoué pour : " + address + " | Statut : " + status);
            mapDiv.innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-light text-center p-5">
                    <i class="fas fa-map-marked-alt fa-4x text-muted mb-4"></i>
                    <h5 class="text-muted">Localisation temporairement indisponible</h5>
                    <p class="text-muted mb-3">${address}</p>
                    <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}"
                       target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-2"></i> Ouvrir dans Google Maps
                    </a>
                </div>
            `;
        }
    });
}

// Si Google Maps échoue complètement à charger
window.addEventListener('error', (e) => {
    if (e.filename && e.filename.includes('google')) {
        document.getElementById('doctor-map').innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-light text-center p-5">
                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
                <h5>Carte indisponible</h5>
                <p class="text-muted">Vérifiez votre connexion ou réessayez plus tard</p>
            </div>
        `;
    }
});
</script>
@endsection
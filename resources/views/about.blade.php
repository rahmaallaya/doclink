@extends('layouts.app')

@section('title', 'À propos')
@section('content')
    <div class="text-center mb-5 p-5 bg-white rounded-5 shadow-lg about-header">
        <h1 class="display-5 text-primary fw-bolder mb-3">DocLink : Simplifiez votre Santé</h1>
        <p class="lead text-secondary fs-4">
            Notre plateforme a été conçue pour **fluidifier la communication** et la gestion des agendas entre les patients et les praticiens.
        </p>
        <div class="divider"></div>
        <p class="text-muted mt-3">DocLink, la passerelle vers des soins simplifiés et efficaces.</p>
    </div>

    <h2 class="text-center mb-5 mt-5 text-dark fw-bold display-6">✨ Nos Engagements et Avantages</h2>

    <div class="row g-4">
        
        {{-- Carte 1 : Facilité de RDV --}}
        <div class="col-md-4">
            <div class="feature-card-apropos">
                <i class="fas fa-calendar-alt feature-icon-apropos text-primary"></i>
                <h3 class="h5 fw-bold mt-3">Réservation 24/7</h3>
                <p class="text-muted">Prenez rendez-vous à tout moment, de jour comme de nuit, sans avoir à décrocher le téléphone.</p>
            </div>
        </div>

        {{-- Carte 2 : Gestion pour les Médecins --}}
        <div class="col-md-4">
            <div class="feature-card-apropos">
                <i class="fas fa-chart-line feature-icon-apropos text-success"></i>
                <h3 class="h5 fw-bold mt-3">Optimisation d'Agenda</h3>
                <p class="text-muted">Pour les professionnels : visualisez et gérez vos disponibilités facilement, réduisant les annulations et les oublis.</p>
            </div>
        </div>

        {{-- Carte 3 : Notifications --}}
        <div class="col-md-4">
            <div class="feature-card-apropos">
                <i class="fas fa-bell feature-icon-apropos text-warning"></i>
                <h3 class="h5 fw-bold mt-3">Rappels Efficaces</h3>
                <p class="text-muted">Recevez des notifications automatiques par e-mail ou SMS pour ne manquer aucune consultation.</p>
            </div>
        </div>
        
    </div>
    
    <div class="row g-4 mt-3 justify-content-center">
        
        {{-- Carte 4 : Sécurité des Données --}}
        <div class="col-md-4">
            <div class="feature-card-apropos">
                <i class="fas fa-lock feature-icon-apropos text-info"></i>
                <h3 class="h5 fw-bold mt-3">Sécurité des Données</h3>
                <p class="text-muted">Vos informations personnelles et médicales sont traitées avec la plus grande confidentialité et sécurité.</p>
            </div>
        </div>

        {{-- Carte 5 : Accès Historique --}}
        <div class="col-md-4">
            <div class="feature-card-apropos">
                <i class="fas fa-history feature-icon-apropos text-danger"></i>
                <h3 class="h5 fw-bold mt-3">Historique Complet</h3>
                <p class="text-muted">Consultez l'historique de vos rendez-vous passés et à venir en un coup d'œil.</p>
            </div>
        </div>
        
    </div>

    <div class="mt-5 p-5 bg-primary text-white rounded-5 text-center shadow-lg">
        <p class="mb-0 fs-4 fw-light">DocLink : La santé connectée, **simplifiée pour tous**.</p>
    </div>

<style>
/* Styles spécifiques à la page À propos pour une identité forte */
.about-header {
    background-color: #f8f9fa !important; /* Léger gris clair */
    border: 1px solid #dee2e6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
}

.about-header .divider {
    height: 3px;
    width: 80px;
    background-color: var(--bs-primary); /* Utilise la couleur primaire de Bootstrap */
    margin: 15px auto;
    border-radius: 5px;
}

.feature-card-apropos {
    text-align: center;
    padding: 30px;
    border-radius: 20px;
    background: #fff;
    transition: all 0.3s ease;
    height: 100%;
    /* Style neumorphisme doux */
    box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.08), -5px -5px 15px rgba(255, 255, 255, 0.8);
}

.feature-card-apropos:hover {
    transform: translateY(-5px);
    box-shadow: 10px 10px 30px rgba(0, 0, 0, 0.15), -10px -10px 30px rgba(255, 255, 255, 1);
}

.feature-icon-apropos {
    font-size: 3.5rem; /* Icones plus grandes */
    margin-bottom: 15px;
    display: block; /* S'assure que l'icône prend toute la largeur pour le centrage */
}

/* Couleurs utilisées (assurez-vous qu'elles correspondent à votre thème Bootstrap) */
.text-primary { color: #0d6efd !important; }
.text-success { color: #198754 !important; }
.text-warning { color: #ffc107 !important; }
.text-info { color: #0dcaf0 !important; }
.text-danger { color: #dc3545 !important; }
</style>
@endsection
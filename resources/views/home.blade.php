{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'DocLink - Prise de rendez-vous médical')

@section('content')
    {{-- ESPACE MAGIQUE : pousse tout sous la navbar --}}


    <div class="min-vh-100 d-flex align-items-center justify-content-center text-white position-relative overflow-hidden hero-section-pro">
        <!-- Overlay sombre et élégant -->
       
        
        <!-- Background discret avec effet de mouvement -->
        <div class="background-image-pro"></div>

        <div class="container position-relative z-10 px-4">
            <div class="text-center mb-5 animate-fade-in-pro">
                <div class="logo-container-pro mb-4">
                    <i class="fas fa-hand-holding-heart logo-icon-pro"></i>
                </div>
                <h1 class="display-2 fw-light mb-4 hero-title-pro">
                    DocLink
                </h1>
                <p class="lead fs-3 mb-5 hero-subtitle-pro">
                    La plateforme de référence pour une gestion médicale simplifiée et sécurisée.
                </p>
            </div>

            {{-- Boutons d'action --}}
            <div class="row justify-content-center g-4 mb-5">
                @guest
                    <div class="col-md-6 col-lg-4 animate-slide-up-pro" style="animation-delay: 0.2s;">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg w-100 py-4 shadow-pro fs-5 btn-chic-effect">
                            <i class="fas fa-user-plus me-2"></i> Créer mon compte
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-4 animate-slide-up-pro" style="animation-delay: 0.4s;">
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg w-100 py-4 shadow-pro fs-5 btn-chic-effect">
                            <i class="fas fa-sign-in-alt me-2"></i> Connexion Espace Pro/Patient
                        </a>
                    </div>
                @else
                    @if(auth()->user()->role === 'patient')
                        <div class="col-md-6 col-lg-4 animate-slide-up-pro" style="animation-delay: 0.2s;">
                            <a href="{{ route('appointments.search') }}" class="btn btn-success btn-lg w-100 py-4 shadow-pro fs-5 btn-chic-effect">
                                <i class="fas fa-search me-2"></i> Trouver un praticien
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4 animate-slide-up-pro" style="animation-delay: 0.4s;">
                            <a href="{{ route('appointments.index') }}" class="btn btn-info btn-lg w-100 py-4 shadow-pro fs-5 btn-chic-effect">
                                <i class="fas fa-calendar-check me-2"></i> Mes rendez-vous
                            </a>
                        </div>

                    @elseif(auth()->user()->role === 'medecin')
                        <div class="col-md-6 col-lg-4 animate-slide-up-pro" style="animation-delay: 0.2s;">
                            <a href="{{ route('appointments.today') }}" class="btn btn-warning btn-lg w-100 py-4 shadow-pro fs-5 btn-chic-effect">
                                <i class="fas fa-list-alt me-2"></i> Agenda du jour
                            </a>
                        </div>
                        <div class="col-md-6 col-lg-4 animate-slide-up-pro" style="animation-delay: 0.4s;">
                            <a href="{{ route('appointments.manage_availabilities') }}" class="btn btn-light text-dark btn-lg w-100 py-4 shadow-pro fs-5 btn-chic-effect">
                                <i class="fas fa-clock me-2"></i> Gérer les disponibilités
                            </a>
                        </div>

                    @elseif(auth()->user()->role === 'admin')
                        <div class="col-12 col-lg-6 animate-slide-up-pro" style="animation-delay: 0.2s;">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-lg w-100 py-4 shadow-pro fs-4 btn-chic-effect">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord Admin
                            </a>
                        </div>
                    @endif
                @endguest
            </div>

            {{-- Avantages --}}
            <div class="row mt-5 pt-5 text-center features-section-pro">
                <div class="col-md-4 mb-4 animate-fade-in-pro" style="animation-delay: 0.6s;">
                    <div class="feature-card-pro">
                        <div class="feature-icon-pro"><i class="fas fa-headset"></i></div>
                        <h5 class="fw-bold mt-3 text-light">Support Continu</h5>
                        <p class="text-light opacity-75">Assistance professionnelle 24/7</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 animate-fade-in-pro" style="animation-delay: 0.8s;">
                    <div class="feature-card-pro">
                        <div class="feature-icon-pro"><i class="fas fa-laptop-medical"></i></div>
                        <h5 class="fw-bold mt-3 text-light">Technologie de Pointe</h5>
                        <p class="text-light opacity-75">Algorithmes d'optimisation d'agenda</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 animate-fade-in-pro" style="animation-delay: 1s;">
                    <div class="feature-card-pro">
                        <div class="feature-icon-pro"><i class="fas fa-lock"></i></div>
                        <h5 class="fw-bold mt-3 text-light">Conformité RGPD</h5>
                        <p class="text-light opacity-75">Confidentialité et sécurité des données</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STYLE (tout est là, rien à toucher ailleurs) --}}
    <style>
        .hero-section-pro { 
            background: #0f1c30; 
            position: relative; 
        }
        .dark-overlay { 
            background: rgba(15, 28, 48, 0.8); 
        }
        .background-image-pro {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: url('https://images.unsplash.com/photo-1542880756-cd9197c36b4a?auto=format&fit=crop&w=2070&q=80') center/cover no-repeat;
            opacity: 0.2;
            transition: all 0.8s ease;
        }
        .hero-section-pro:hover .background-image-pro { 
            opacity: 0.3; 
            transform: scale(1.02); 
        }

        .logo-container-pro { 
            display: inline-block; padding: 25px; background: rgba(255,255,255,0.1); 
            border: 3px solid #66b2ff; border-radius: 50%; box-shadow: 0 0 20px #66b2ff40; 
        }
        .logo-icon-pro { 
            font-size: 3.5rem; color: #66b2ff; text-shadow: 0 0 10px rgba(102,178,255,0.5); 
        }
        .hero-title-pro { 
            color: #fff; letter-spacing: 2px; text-shadow: 1px 1px 3px rgba(0,0,0,0.5); 
        }
        .hero-subtitle-pro { 
            font-weight: 300; text-shadow: 1px 1px 2px rgba(0,0,0,0.4); 
        }

        .btn-chic-effect {
            border-radius: 12px; font-weight: 600; transition: all 0.3s ease;
        }
        .btn-chic-effect:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .feature-card-pro {
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px; padding: 2rem 1rem; transition: all 0.3s ease;
        }
        .feature-card-pro:hover { 
            background: rgba(255,255,255,0.08); 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.4); 
        }
        .feature-icon-pro {
            width: 60px; height: 60px; background: #66b2ff; color: #0f1c30;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin: 0 auto; font-size: 1.8rem;
        }

        /* Espace sous la navbar (responsive) */
        .pt-16 { padding-top: 6rem; }     /* 96px  */
        @media (min-width: 768px) { .pt-md-20 { padding-top: 8rem; } }   /* 128px */
        @media (min-width: 992px) { .pt-lg-24 { padding-top: 10rem; } }  /* 160px */

        @keyframes fadeInPro { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUpPro { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-pro { animation: fadeInPro 1s ease-out forwards; }
        .animate-slide-up-pro { animation: slideUpPro 0.8s ease-out forwards; }
    </style>
@endsection
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DocLink - Plateforme de prise de rendez-vous et messagerie sécurisée médecin-patient">
    <title>DocLink - @yield('title', 'Santé connectée')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

   <style>
    :root {
        --primary: #007bff;
        --primary-dark: #0056b3;
        --danger: #dc3545;
    }
    * { font-family: 'Poppins', sans-serif; }
    body {
        background: linear-gradient(to bottom, #f0f4f8, #d9e2ec);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .navbar {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
        box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        padding: 1rem 0;
    }
    .navbar-brand {
        font-weight: 800;
        font-size: 1.9rem;
        color: white !important;
        text-shadow: 2px 2px 10px rgba(0,0,0,0.4);
    }
    .nav-link {
        color: white !important;
        font-weight: 500;
        padding: 0.6rem 1.2rem !important;
        border-radius: 50px;
        transition: all .3s ease;
    }
    .nav-link:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
    }

    /* BADGE CLIGNOTANT */
    .unread-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ff3b30;
        color: white;
        font-size: 0.7rem;
        font-weight: bold;
        min-width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        animation: pulse 1.8s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255,59,48,0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255,59,48,0); }
        100% { box-shadow: 0 0 0 0 rgba(255,59,48,0); }
    }

    /* DROPDOWN NOTIFICATIONS – VERSION PARFAITE */
    .notification-dropdown {
        width: 380px !important;
        max-height: 80vh;
        overflow: hidden;
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
    }

    .notification-dropdown .dropdown-header {
        border-radius: 16px 16px 0 0 !important;
        font-weight: 600;
    }

    /* Conteneur avec scroll si trop de notifs */
    #notifications-list {
        max-height: 420px;
        overflow-y: auto;
        padding: 0 8px;
    }

    /* Chaque notification – ne dépasse PLUS JAMAIS */
    .notification-item-link {
        display: block;
        color: inherit;
        text-decoration: none;
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        transition: background 0.2s;
    }
    .notification-item-link:hover {
        background: #f8f9fa;
        border-radius: 12px;
        margin: 4px 8px;
    }

    .notification-content {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .icon-circle {
        width: 42px;
        height: 42px;
        background: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: white;
        font-size: 1.1rem;
    }

    .notification-text {
        font-size: 0.875rem;
        line-height: 1.5;
        margin: 0;
        color: #333;
        flex: 1;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #888;
        margin-top: 4px;
    }

    .app-content {
        padding: 100px 20px 60px;
        flex-grow: 1;
    }

    .footer {
        background: #212529;
        color: #adb5bd;
        padding: 2.5rem 0;
        margin-top: auto;
    }
    .footer a { color: #adb5bd; text-decoration: none; }
    .footer a:hover { color: white; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">DocLink</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">À propos</a></li>

                @auth
                    @if(auth()->user()->role === 'patient')
                        <li class="nav-item"><a class="nav-link" href="{{ route('appointments.search') }}">Trouver un médecin</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}">Mes RDV</a></li>
                    @elseif(auth()->user()->role === 'medecin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('appointments.today') }}">RDV du jour</a></li>
                         <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}">Mes RDV</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('appointments.manage_availabilities') }}">Disponibilités</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="{{ route('questions.index') }}">Forum</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin_messages.index') }}">Support</a></li>
                @endauth
                @if(auth()->user() && auth()->user()->role === 'admin')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.categories.index') }}">Gestion Catégories</a>
    </li>
@endif
       
            </ul>
                
            <ul class="navbar-nav align-items-center">
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Connexion</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light px-4" href="{{ route('register') }}">S'inscrire</a></li>
                @else
                    <!-- CLOCHE NOTIFICATIONS -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link position-relative p-0" href="#" role="button" data-bs-toggle="dropdown"
                           id="notificationDropdown" aria-expanded="false">
                            <i class="fas fa-bell fa-lg"></i>
                            <span class="unread-badge" id="notification-badge" style="display: none;">
                                <span id="notification-count">0</span>
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 notification-dropdown">
                            <li><h6 class="dropdown-header bg-primary text-white">Notifications</h6></li>
                            <div id="notifications-list">
                                <div class="text-center text-muted py-4">Chargement...</div>
                            </div>
                            <li>
                                <a href="{{ route('notifications.index') }}" class="dropdown-item text-center text-primary fw-bold py-3">
                                    Voir toutes les notifications
                                </a>
                            </li>
                        </ul>
                    </li>
                 <!-- PROFIL -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=007bff&color=fff&size=80' }}"
                                 alt="Avatar" class="user-avatar me-2">
                            <div class="text-start">
                                <div class="fw-bold">{{ Str::limit(auth()->user()->name, 15) }}</div>
                                <small class="text-white-50">{{ ucfirst(auth()->user()->role) }}</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mon profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('messages.index') }}">Messagerie</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <button type="submit" class="dropdown-item text-danger">Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main class="@yield('main-class', 'app-content container')">
    @yield('content')
</main>

<footer class="footer text-center">
    <div class="container">
        <p class="mb-0">© {{ date('Y') }} <strong class="text-white">DocLink</strong> • Plateforme de santé connectée</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const badge = document.getElementById('notification-badge');
    const countEl = document.getElementById('notification-count');
    const list = document.getElementById('notifications-list');

    function loadNotifications() {
        fetch('{{ route('notifications.api.unread') }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(r => r.json())
        .then(data => {
            // Badge
            if (data.unread_count > 0) {
                countEl.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }

            // Liste (seulement si dropdown ouvert)
            if (document.querySelector('#notificationDropdown[aria-expanded="true"]')) {
                if (data.notifications.length === 0) {
                    list.innerHTML = '<div class="dropdown-item text-center py-4 text-muted">Aucune notification</div>';
                } else {
                    list.innerHTML = data.notifications.map(n => `
                        <li>
                            <a href="${n.url}" class="dropdown-item border-bottom py-3">
                                <div class="d-flex">
                                    <div class="icon-circle bg-primary text-white">
                                        <i class="${n.icon}"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-1 fw-medium">${n.message}</p>
                                        <small class="text-muted">${n.time}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                    `).join('');
                }
            }
        })
        .catch(() => {});
    }

    // Chargement initial + toutes les 10 secondes
    loadNotifications();
    setInterval(loadNotifications, 10000);

    // Marquer comme lues quand on ouvre la cloche
    document.getElementById('notificationDropdown').addEventListener('shown.bs.dropdown', function () {
        if (badge.style.display === 'flex') {
            fetch('{{ route('notifications.api.mark-all-read') }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => loadNotifications());
        }
    });
});
</script>
</body>
</html>
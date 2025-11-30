<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Notification;

class NotificationComposer
{
    public function compose(View $view)
    {
        // ID utilisateur temporaire pour les tests
        $userId = 1;
        
        // Récupérer toutes les notifications de l'utilisateur (limitées aux 10 dernières)
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Compter les notifications non lues
        $unreadNotificationsCount = Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
        
        // Partager avec la vue
        $view->with([
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadNotificationsCount
        ]);
    }
}
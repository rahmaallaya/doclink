<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $notifications = $this->service->getForUser(Auth::user());
        $unreadCount   = $this->service->getUnreadCount(Auth::user());

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function show($id)
    {
        $notification = $this->service->markAsRead($id, Auth::user());
        return view('notifications.show', compact('notification'));
    }

    public function markAllAsRead()
    {
        $this->service->markAllAsRead(Auth::user());
        return back()->with('success', 'Toutes les notifications sont marquées comme lues !');
    }
}
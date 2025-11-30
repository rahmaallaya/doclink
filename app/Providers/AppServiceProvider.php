<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Services\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository binding Sprint 1
        $this->app->bind(
            \App\Repositories\Interfaces\AppointmentRepositoryInterface::class,
            \App\Repositories\AppointmentRepository::class
        );

        // Repository bindings Sprint 2
        $this->app->bind(
            \App\Repositories\Interfaces\PrivateMessageRepositoryInterface::class,
            \App\Repositories\PrivateMessageRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\QuestionRepositoryInterface::class,
            \App\Repositories\QuestionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\AdminMessageRepositoryInterface::class,
            \App\Repositories\AdminMessageRepository::class
        );

        // Bind du NotificationService
        $this->app->bind(NotificationService::class, function ($app) {
            return new NotificationService();
        });
    }

    public function boot(): void
    {View::composer('layouts.app', function ($view) {
        $view->with('unreadNotifications', 0);
        $view->with('user', null);
    });
    }}
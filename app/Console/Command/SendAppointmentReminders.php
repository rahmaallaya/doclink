<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send appointment reminders to patients';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $count = $this->notificationService->sendScheduledReminders();
        
        if ($count > 0) {
            $this->info("{$count} rappels de rendez-vous envoyés avec succès.");
        } else {
            $this->info("Aucun rappel à envoyer pour le moment.");
        }
        
        return 0;
    }
}
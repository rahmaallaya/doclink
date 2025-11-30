<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'password',
        'role',           // 'patient' | 'medecin' | 'admin'
        'specialty',      // pour les médecins
        'location',       // ville/adresse
        'phone',          // téléphone
        'bio',            // présentation
        'avatar',         // photo de profil
        'status',         // ACTIVE | PENDING_VALIDATION | REJECTED | SUSPENDED
    ];

    protected $hidden = [
        'password', 
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    // ====================== NOTIFICATIONS PERSONNALISÉES ======================
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id')
                    ->orWhere(function ($query) {
                        $query->whereNull('receiver_id')
                              ->where('receiver_specialty', $this->specialty);
                    })
                    ->orderByDesc('created_at');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('read', false);
    }
    // =========================================================================

    // ====================== RELATIONS ======================
    public function availabilities()       { return $this->hasMany(Availability::class, 'doctor_id'); }
    public function appointmentsAsPatient(){ return $this->hasMany(Appointment::class, 'user_id'); }
    public function appointmentsAsDoctor() { return $this->hasMany(Appointment::class, 'doctor_id'); }
    public function sentMessages()         { return $this->hasMany(PrivateMessage::class, 'sender_id'); }
    public function receivedMessages()     { return $this->hasMany(PrivateMessage::class, 'receiver_id'); }
    public function questions()            { return $this->hasMany(Question::class, 'patient_id'); }
    public function answers()              { return $this->hasMany(Answer::class, 'doctor_id'); }
    public function adminMessages()        { return $this->hasMany(AdminMessage::class); }

    // ====================== ACCESSEURS ======================
    
    /**
     * Accesseur pour l'avatar - génère un avatar par défaut si non défini
     */
    public function getAvatarAttribute($value)
    {
        if ($value && file_exists(storage_path('app/public/' . $value))) {
            return asset('storage/' . $value);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) 
               . '&background=007bff&color=fff&size=200&bold=true';
    }
   
 
public function getSpecialtyAttribute($value)
{
    // $this->role → Laravel va chercher la colonne "role" automatiquement si elle n’est pas déjà chargée
    if ($this->role === 'admin') {
        return 'Administration';
    }

    return $value ?? 'Non renseignée';
}

    // ====================== MÉTHODES UTILES ======================
    
    /**
     * Vérifie si l'utilisateur est un patient
     */
    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    /**
     * Vérifie si l'utilisateur est un médecin
     */
    public function isDoctor(): bool
    {
        return $this->role === 'medecin';
    }

    /**
     * Vérifie si l'utilisateur est un admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si le compte est actif
     */
    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }

    /**
     * Vérifie si le compte est en attente de validation
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDING_VALIDATION';
    }

    /**
     * Obtenir le nombre de rendez-vous à venir (pour médecins)
     */
    public function getUpcomingAppointmentsCount(): int
    {
        if (!$this->isDoctor()) {
            return 0;
        }

        return $this->appointmentsAsDoctor()
                    ->where('status', 'planned')
                    ->where('appointment_time', '>', now())
                    ->count();
    }

    /**
     * Obtenir le nombre total de patients vus (pour médecins)
     */
    public function getTotalPatientsCount(): int
    {
        if (!$this->isDoctor()) {
            return 0;
        }

        return $this->appointmentsAsDoctor()
                    ->where('status', '!=', 'cancelled')
                    ->distinct('user_id')
                    ->count('user_id');
    }

    /**
     * Obtenir l'adresse complète formatée
     */
    public function getFullAddressAttribute(): string
    {
        return $this->location ?? 'Adresse non renseignée';
    }

    /**
     * Obtenir le nom complet avec titre (Dr. pour médecins)
     */
    public function getFullNameAttribute(): string
    {
        return $this->isDoctor() ? "Dr. {$this->name}" : $this->name;
    }
}
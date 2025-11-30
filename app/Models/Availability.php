<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Availability extends Model
{
    protected $fillable = ['doctor_id', 'start_time', 'end_time', 'booked'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'booked'     => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // RELATION CRUCIALE POUR SAVOIR SI LE CRÉNEAU EST RÉSERVÉ
    public function appointment()
    {
        return $this->hasOne(Appointment::class, 'availability_id');
    }

    // Optionnel : accesseurs pour forcer Carbon (utile si tu utilises $dates ailleurs)
    public function getStartTimeAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getEndTimeAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }
    public function isBooked()
{
    return $this->appointment()->whereIn('status', ['planned', 'confirmed'])->exists();
}

public function isAvailable()
{
    return !$this->isBooked() && Carbon::parse($this->start_time)->isFuture();
}
}

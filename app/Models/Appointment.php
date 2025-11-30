<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'doctor_id', 'availability_id', 'appointment_time', 'end_time', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }
    // app/Models/Appointment.php

public function patient()
{
    return $this->belongsTo(User::class, 'user_id');
}


}
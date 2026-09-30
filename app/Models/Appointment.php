<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'doctor_shedule_id',
        'appointment_date',
        'appointment_time',
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Appointment kis doctor ke sath hai
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Appointment kis schedule slot se book hui
    public function schedule()
    {
        return $this->belongsTo(DoctorShedule::class, 'doctor_shedule_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}

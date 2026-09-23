<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'qualification',
        'specialization',
        'image',
        'consultation_fee',
        'status',
    ];

    protected $casts = [
        'consultation_fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function schedules()
    {
        return $this->hasMany(DoctorShedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}

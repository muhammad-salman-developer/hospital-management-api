<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorShedule extends Model
{
    protected $table = 'doctor_shedule';

    protected $fillable = [
        'doctor_id',
        'day',
        'start_time',
        'end_time',
        'is_available',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}

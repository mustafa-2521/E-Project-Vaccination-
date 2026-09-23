<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'location',
        'license_no',
        'consultation_fee',
        'vaccination_fee',
        'opening_hours',
        'facilities',
        'vaccine_status',
    ];

    protected $casts = [
        'consultation_fee' => 'decimal:2',
        'vaccination_fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}

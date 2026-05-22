<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'blood_type',
        'allergies',
        'medical_conditions',
        'current_medications',   // real column in DB
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'notes',
        // doctor_name and doctor_phone exist in the form but not yet in the table
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

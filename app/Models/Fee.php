<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester_id',
        'name',
        'name_ar',
        'fee_type',
        'amount',
        'target',
        'major_id',
        'academic_year',
        'due_date',
        'description',
        'is_active',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, StudentFee::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_fees')
            ->withPivot(['amount', 'paid_amount', 'status', 'due_date'])
            ->withTimestamps();
    }
}

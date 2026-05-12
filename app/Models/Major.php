<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'name_ar',
        'code',
        'description',
        'description_ar',
        'degree',
        'years',
        'years_required',
        'credits_required',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function students()
    {
        return $this->hasMany(User::class, 'major_id');
    }

    /**
     * Get the courses for this major.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    /**
     * Get the years attribute, fallback to years_required for backward compatibility.
     */
    public function getYearsAttribute()
    {
        return $this->attributes['years'] ?? $this->attributes['years_required'] ?? 4;
    }
}

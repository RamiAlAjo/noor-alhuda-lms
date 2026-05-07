<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeLockHistory extends Model
{
    use HasFactory;

    protected $table = 'grade_lock_history';

    protected $fillable = [
        'lockable_type',
        'lockable_id',
        'locked',
        'performed_by',
        'reason',
    ];

    protected $casts = [
        'locked' => 'boolean',
    ];

    /**
     * Get the lockable model.
     */
    public function lockable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the action.
     */
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Scope to get lock actions.
     */
    public function scopeLocks($query)
    {
        return $query->where('locked', true);
    }

    /**
     * Scope to get unlock actions.
     */
    public function scopeUnlocks($query)
    {
        return $query->where('locked', false);
    }
}

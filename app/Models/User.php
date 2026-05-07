<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'status',
        'is_active',
        'password',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'password' => 'hashed', // Temporarily disabled for testing
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's settings.
     */
    public function settings(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get the user's medical record.
     */
    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class, 'student_id');
    }

    /**
     * Get the courses taught by this user (if teacher).
     */
    public function taughtCourses(): BelongsToMany
    {
        return $this->belongsToMany(CourseOffering::class, 'course_teachers', 'teacher_id', 'course_offering_id')
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * Get the enrollments for this user (if student).
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * Get the enrolled courses for this user (if student).
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(CourseOffering::class, 'enrollments', 'student_id', 'course_offering_id')
            ->withPivot('status', 'semester_id', 'approved_at')
            ->wherePivot('status', 'approved')
            ->withTimestamps();
    }

    /**
     * Get the student's grades.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class, 'student_id');
    }

    /**
     * Get the student's attendance records.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    /**
     * Get notes for this user.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Get the submissions for this user.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    /**
     * Get the quiz attempts for this user.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'student_id');
    }

    /**
     * Get calendar events for this user.
     */
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    /**
     * Get tasks for this user.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get sent messages.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get received messages.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the user's full name from profile.
     */
    public function getFullNameAttribute(): string
    {
        if ($this->profile) {
            return $this->profile->full_name;
        }

        return $this->name;
    }

    /**
     * Get major for this user.
     */
    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Get payments for this user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    /**
     * Get the user's unique ID.
     * Returns the user_id from users table, falls back to profile's user_id_unique for backwards compatibility.
     */
    public function getUserIdAttribute($value): ?string
    {
        // Return the user_id from the users table if it exists
        return $value;
    }

    /**
     * Get the user's initials.
     */
    public function initials(): string
    {
        if ($this->profile && $this->profile->first_name) {
            $first = Str::substr($this->profile->first_name, 0, 1);
            $last = $this->profile->last_name ? Str::substr($this->profile->last_name, 0, 1) : '';

            return strtoupper($first.$last);
        }

        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is teacher.
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /**
     * Check if user is student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Check if user account is active.
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    /**
     * Check if user account is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if user account is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Get avatar URL or return default.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }

        // Return default avatar based on gender if available
        if ($this->profile && $this->profile->gender) {
            return asset('images/avatar-'.$this->profile->gender.'.png');
        }

        return asset('images/avatar-default.png');
    }

    /**
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope to get only inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where(function ($q) {
            $q->where('is_active', false)->orWhere('status', '!=', 'active');
        });
    }

    /**
     * Get the user's primary role.
     */
    public function getPrimaryRoleAttribute(): ?string
    {
        return $this->roles()->first()?->name;
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Get unread messages count.
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        return $this->receivedMessages()->unread()->count();
    }

    /**
     * Get pending tasks count.
     */
    public function getPendingTasksCountAttribute(): int
    {
        return $this->tasks()->pending()->count();
    }

    /**
     * Scope to get only students.
     */
    public function scopeStudents($query)
    {
        return $query->role('student');
    }

    /**
     * Scope to get only teachers.
     */
    public function scopeTeachers($query)
    {
        return $query->role('teacher');
    }

    /**
     * Scope to get only admins.
     */
    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }

    /**
     * Generate a unique Student ID with sequential numbering.
     * Format: YYYY-NNNNN (e.g., 2024-00001)
     */
    public static function generateStudentId(): string
    {
        $year = date('Y');

        // Find the last student with the same year
        $lastUser = static::where('user_id', 'like', "{$year}-%")
            ->whereHas('roles', function ($query) {
                $query->where('name', 'student');
            })
            ->orderByDesc('user_id')
            ->first();

        if ($lastUser && $lastUser->user_id) {
            // Extract the sequential number from the last student ID
            $parts = explode('-', $lastUser->user_id);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: YEAR-NNNNN (e.g., 2024-00001)
        return "{$year}-".str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique Teacher ID with sequential numbering.
     * Format: T-YYYY-NNNNN (e.g., T-2024-00001)
     */
    public static function generateTeacherId(): string
    {
        $year = date('Y');

        // Find the last teacher with the same year
        $lastUser = static::where('user_id', 'like', "T-{$year}-%")
            ->whereHas('roles', function ($query) {
                $query->where('name', 'teacher');
            })
            ->orderByDesc('user_id')
            ->first();

        if ($lastUser && $lastUser->user_id) {
            // Extract the sequential number from the last teacher ID
            $parts = explode('-', $lastUser->user_id);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: T-YEAR-NNNNN (e.g., T-2024-00001)
        return "T-{$year}-".str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate email based on name.
     * Format: firstname.lastname@institution.edu
     */
    public static function generateEmail(string $firstName, string $lastName, string $domain = 'institution.edu'): string
    {
        $firstName = strtolower(trim($firstName));
        $lastName = strtolower(trim($lastName));

        // Remove any special characters
        $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);
        $lastName = preg_replace('/[^a-z0-9]/', '', $lastName);

        $baseEmail = "{$firstName}.{$lastName}";
        $email = "{$baseEmail}@{$domain}";

        // Check if email exists and add number if needed
        $counter = 1;
        while (static::where('email', $email)->exists()) {
            $email = "{$baseEmail}{$counter}@{$domain}";
            $counter++;
        }

        return $email;
    }

    /**
     * Generate a unique user ID with sequential numbering.
     * Format: STU-2024-0001, TCH-2024-0001, ADM-2024-0001
     *
     * @deprecated Use generateStudentId() or generateTeacherId() instead
     */
    public static function generateUserId(string $role): string
    {
        return match ($role) {
            'student' => static::generateStudentId(),
            'teacher' => static::generateTeacherId(),
            'admin' => static::generateAdminId(),
            default => static::generateGenericUserId($role),
        };
    }

    /**
     * Generate Admin ID.
     */
    protected static function generateAdminId(): string
    {
        $prefix = 'ADM';
        $year = date('Y');

        $lastUser = static::where('user_id', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('user_id')
            ->first();

        if ($lastUser && $lastUser->user_id) {
            $parts = explode('-', $lastUser->user_id);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "{$prefix}-{$year}-".str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate generic user ID.
     */
    protected static function generateGenericUserId(string $role): string
    {
        $prefix = strtoupper(substr($role, 0, 3));
        $year = date('Y');

        $lastUser = static::where('user_id', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('user_id')
            ->first();

        if ($lastUser && $lastUser->user_id) {
            $parts = explode('-', $lastUser->user_id);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "{$prefix}-{$year}-".str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}

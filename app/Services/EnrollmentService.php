<?php

namespace App\Services;

use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * EnrollmentService
 *
 * Handles enrollment business logic including enrollment creation,
 * validation, and management operations.
 */
class EnrollmentService
{
    /**
     * Enroll a student in a course offering.
     *
     *
     * @throws \Exception
     */
    public function enrollStudent(
        int $studentId,
        int $courseOfferingId,
        ?int $semesterId = null,
        ?string $notes = null
    ): Enrollment {
        return DB::transaction(function () use ($studentId, $courseOfferingId, $semesterId, $notes) {
            // Validate student exists
            $student = User::findOrFail($studentId);

            // Validate course offering exists and is active
            $courseOffering = CourseOffering::findOrFail($courseOfferingId);

            if (! $courseOffering->is_active) {
                throw new \Exception('Course offering is not active for enrollment.');
            }

            // Check if already enrolled
            $existingEnrollment = Enrollment::where('student_id', $studentId)
                ->where('course_offering_id', $courseOfferingId)
                ->whereIn('status', ['pending', 'approved'])
                ->lockForUpdate()
                ->first();

            if ($existingEnrollment) {
                throw new \Exception('Student is already enrolled in this course offering.');
            }

            // Check capacity with atomic increment to prevent race conditions
            $affected = DB::table('course_offerings')
                ->where('id', $courseOfferingId)
                ->where('current_students', '<', DB::raw('max_students'))
                ->increment('current_students');

            if ($affected === 0) {
                throw new \Exception('Course offering has reached maximum capacity.');
            }

            // Get current semester if not provided
            if (! $semesterId) {
                $currentSemester = Semester::where('is_current', true)->first();
                $semesterId = $currentSemester?->id;
            }

            // Create enrollment with sanitized notes
            $enrollment = Enrollment::create([
                'student_id' => $studentId,
                'course_offering_id' => $courseOfferingId,
                'semester_id' => $semesterId,
                'status' => Enrollment::STATUS_PENDING,
                'enrolled_at' => now(),
                'notes' => $notes ? strip_tags($notes) : null,
            ]);

            Log::info('Student enrolled successfully', [
                'student_id' => $studentId,
                'course_offering_id' => $courseOfferingId,
                'enrollment_id' => $enrollment->id,
            ]);

            return $enrollment;
        }, 5);
    }

    /**
     * Approve an enrollment.
     *
     *
     * @throws \Exception
     */
    public function approveEnrollment(int $enrollmentId, ?int $approvedBy = null): Enrollment
    {
        return DB::transaction(function () use ($enrollmentId, $approvedBy) {
            $enrollment = Enrollment::findOrFail($enrollmentId);

            if ($enrollment->status !== Enrollment::STATUS_PENDING) {
                throw new \Exception('Only pending enrollments can be approved.');
            }

            $enrollment->update([
                'status' => Enrollment::STATUS_APPROVED,
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);

            Log::info('Enrollment approved', [
                'enrollment_id' => $enrollmentId,
                'approved_by' => $approvedBy,
            ]);

            return $enrollment->fresh();
        });
    }

    /**
     * Drop an enrollment.
     *
     *
     * @throws \Exception
     */
    public function dropEnrollment(int $enrollmentId, ?string $reason = null): Enrollment
    {
        return DB::transaction(function () use ($enrollmentId, $reason) {
            $enrollment = Enrollment::findOrFail($enrollmentId);

            if ($enrollment->status === Enrollment::STATUS_DROPPED) {
                throw new \Exception('Enrollment is already dropped.');
            }

            if ($enrollment->status === Enrollment::STATUS_COMPLETED) {
                throw new \Exception('Cannot drop a completed enrollment.');
            }

            $enrollment->update([
                'status' => Enrollment::STATUS_DROPPED,
                'dropped_at' => now(),
                'notes' => $reason ? strip_tags($reason) : $enrollment->notes,
            ]);

            // Decrement enrollment count
            if ($enrollment->courseOffering) {
                $enrollment->courseOffering->decrement('current_students');
            }

            Log::info('Enrollment dropped', [
                'enrollment_id' => $enrollmentId,
                'reason' => $reason,
            ]);

            return $enrollment->fresh();
        }, 5);
    }

    /**
     * Get enrollments for a student.
     */
    public function getStudentEnrollments(int $studentId, ?string $status = null): Collection
    {
        $query = Enrollment::where('student_id', $studentId)
            ->with(['courseOffering.course', 'semester']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Get enrollments for a course offering.
     */
    public function getCourseOfferingEnrollments(int $courseOfferingId, ?string $status = null): Collection
    {
        $query = Enrollment::where('course_offering_id', $courseOfferingId)
            ->with(['student', 'semester']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Check if a student is enrolled in a course offering.
     */
    public function isStudentEnrolled(int $studentId, int $courseOfferingId): bool
    {
        return Enrollment::where('student_id', $studentId)
            ->where('course_offering_id', $courseOfferingId)
            ->whereIn('status', [Enrollment::STATUS_PENDING, Enrollment::STATUS_APPROVED])
            ->exists();
    }

    /**
     * Get enrollment statistics for a course offering.
     */
    public function getEnrollmentStatistics(int $courseOfferingId): array
    {
        $enrollments = Enrollment::where('course_offering_id', $courseOfferingId)->get();

        return [
            'total' => $enrollments->count(),
            'pending' => $enrollments->where('status', Enrollment::STATUS_PENDING)->count(),
            'approved' => $enrollments->where('status', Enrollment::STATUS_APPROVED)->count(),
            'dropped' => $enrollments->where('status', Enrollment::STATUS_DROPPED)->count(),
            'completed' => $enrollments->where('status', Enrollment::STATUS_COMPLETED)->count(),
        ];
    }
}

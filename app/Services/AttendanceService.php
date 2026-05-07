<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AttendanceService
 *
 * Handles attendance tracking business logic including attendance recording,
 * statistics calculation, and attendance management operations.
 */
class AttendanceService
{
    /**
     * Record attendance for a student.
     *
     *
     * @throws \Exception
     */
    public function recordAttendance(
        int $studentId,
        int $enrollmentId,
        string $status,
        ?string $notes = null,
        ?int $recordedBy = null
    ): Attendance {
        return DB::transaction(function () use ($studentId, $enrollmentId, $status, $notes, $recordedBy) {
            // Validate student exists
            $student = User::findOrFail($studentId);

            // Validate enrollment exists
            $enrollment = Enrollment::findOrFail($enrollmentId);

            // Validate status
            $validStatuses = ['present', 'absent', 'late', 'excused'];
            if (! in_array($status, $validStatuses)) {
                throw new \Exception('Invalid attendance status. Must be one of: '.implode(', ', $validStatuses));
            }

            // Create attendance record with sanitized notes
            $attendance = Attendance::create([
                'student_id' => $studentId,
                'course_offering_id' => $enrollment->course_offering_id,
                'enrollment_id' => $enrollmentId,
                'date' => now()->toDateString(),
                'status' => $status,
                'notes' => $notes ? strip_tags($notes) : null,
                'marked_by' => $recordedBy,
            ]);

            Log::info('Attendance recorded', [
                'student_id' => $studentId,
                'enrollment_id' => $enrollmentId,
                'status' => $status,
            ]);

            return $attendance;
        }, 5);
    }

    /**
     * Get attendance records for a student in a course.
     */
    public function getStudentAttendance(int $studentId, int $enrollmentId): Collection
    {
        return Attendance::where('student_id', $studentId)
            ->where('enrollment_id', $enrollmentId)
            ->orderBy('recorded_at', 'desc')
            ->get();
    }

    /**
     * Get attendance statistics for a student in a course.
     */
    public function getStudentAttendanceStatistics(int $studentId, int $enrollmentId): array
    {
        $stats = Attendance::where('student_id', $studentId)
            ->where('enrollment_id', $enrollmentId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused
            ')
            ->first();

        if ($stats->total === 0) {
            return [
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
                'attendance_rate' => 0,
            ];
        }

        // Calculate attendance rate (present + late count as attended)
        $attended = $stats->present + $stats->late;
        $attendanceRate = round(($attended / $stats->total) * 100, 2);

        return [
            'total' => (int) $stats->total,
            'present' => (int) $stats->present,
            'absent' => (int) $stats->absent,
            'late' => (int) $stats->late,
            'excused' => (int) $stats->excused,
            'attendance_rate' => $attendanceRate,
        ];
    }

    /**
     * Get attendance records for an enrollment.
     */
    public function getEnrollmentAttendance(int $enrollmentId): Collection
    {
        return Attendance::where('enrollment_id', $enrollmentId)
            ->with('student')
            ->orderBy('recorded_at', 'desc')
            ->get();
    }

    /**
     * Get attendance statistics for an enrollment.
     */
    public function getEnrollmentAttendanceStatistics(int $enrollmentId): array
    {
        $stats = Attendance::where('enrollment_id', $enrollmentId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused
            ')
            ->first();

        if ($stats->total === 0) {
            return [
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
                'attendance_rate' => 0,
            ];
        }

        $attended = $stats->present + $stats->late;
        $attendanceRate = round(($attended / $stats->total) * 100, 2);

        return [
            'total' => (int) $stats->total,
            'present' => (int) $stats->present,
            'absent' => (int) $stats->absent,
            'late' => (int) $stats->late,
            'excused' => (int) $stats->excused,
            'attendance_rate' => $attendanceRate,
        ];
    }

    /**
     * Bulk record attendance for multiple students.
     */
    public function bulkRecordAttendance(array $attendanceData, ?int $recordedBy = null): Collection
    {
        return DB::transaction(function () use ($attendanceData, $recordedBy) {
            $records = collect();

            foreach ($attendanceData as $data) {
                $record = $this->recordAttendance(
                    $data['student_id'],
                    $data['enrollment_id'],
                    $data['status'],
                    $data['notes'] ?? null,
                    $recordedBy
                );

                $records->push($record);
            }

            return $records;
        });
    }

    /**
     * Get students with low attendance for an enrollment.
     */
    public function getStudentsWithLowAttendance(int $enrollmentId, float $threshold = 75.0): Collection
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);

        // Use database aggregation to get all students' attendance stats in one query
        $lowAttendanceStudents = DB::table('attendance_records')
            ->join('users', 'attendance_records.student_id', '=', 'users.id')
            ->where('attendance_records.enrollment_id', $enrollmentId)
            ->select('attendance_records.student_id')
            ->selectRaw('
                users.name as student_name,
                users.email as student_email,
                COUNT(*) as total,
                SUM(CASE WHEN status IN ("present", "late") THEN 1 ELSE 0 END) as attended,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                ROUND((SUM(CASE WHEN status IN ("present", "late") THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_rate
            ')
            ->groupBy('attendance_records.student_id', 'users.name', 'users.email')
            ->havingRaw('attendance_rate < ?', [$threshold])
            ->orderBy('attendance_rate', 'asc')
            ->get();

        return $lowAttendanceStudents;
    }
}

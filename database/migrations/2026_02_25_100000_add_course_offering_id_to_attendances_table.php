<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Add student_id if it doesn't exist (it's referenced in the model but not in original migration)
            if (! Schema::hasColumn('attendances', 'student_id')) {
                $table->unsignedBigInteger('student_id')->nullable();
                $table->foreign('student_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
            if (! Schema::hasColumn('attendances', 'course_offering_id')) {
                $table->unsignedBigInteger('course_offering_id')->nullable();
                $table->foreign('course_offering_id')
                    ->references('id')
                    ->on('course_offerings')
                    ->onDelete('cascade');
            }
        });

        // Populate course_offering_id and student_id from enrollments table
        // This assumes there's already data in the enrollments table
        // Use a subquery approach that's compatible with SQLite
        try {
            DB::statement('UPDATE attendances SET course_offering_id = (SELECT course_offering_id FROM enrollments WHERE enrollments.id = attendances.enrollment_id), student_id = COALESCE(student_id, (SELECT student_id FROM enrollments WHERE enrollments.id = attendances.enrollment_id))');
        } catch (\Exception $e) {
            // If the update fails (e.g., no enrollments), just continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['course_offering_id']);
            $table->dropColumn('course_offering_id');
            $table->dropColumn('student_id');
        });
    }
};

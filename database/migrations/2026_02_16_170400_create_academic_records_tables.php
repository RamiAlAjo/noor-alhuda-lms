<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Attendance Records
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_section_id');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'excused', 'late'])->default('present');
            $table->unsignedBigInteger('marked_by')->nullable();
            $table->timestamp('marked_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_section_id')->references('id')->on('course_sections')->onDelete('cascade');
            $table->foreign('marked_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['student_id', 'course_section_id', 'date']);
        });

        // Excuse Requests
        Schema::create('excuse_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_section_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_section_id')->references('id')->on('course_sections')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });

        // Assessment Types
        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('code');
            $table->integer('weight')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Assessments
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_offering_id');
            $table->unsignedBigInteger('assessment_type_id');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->decimal('max_grade', 5, 2)->default(100);
            $table->decimal('max_score', 5, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('type_id')->nullable();
            $table->decimal('total_points', 5, 2)->nullable();
            $table->integer('duration')->nullable();
            $table->timestamps();

            $table->foreign('course_offering_id')->references('id')->on('course_sections')->onDelete('cascade');
            $table->foreign('assessment_type_id')->references('id')->on('assessment_types')->onDelete('cascade');
        });

        // Student Grades
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('assessment_id');
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->text('feedback')->nullable();
            $table->unsignedBigInteger('graded_by')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->text('submission_path')->nullable();
            $table->text('submission_text')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['student_id', 'assessment_id']);
        });

        // Grade Appeals
        Schema::create('grade_appeals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('student_grade_id');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->decimal('adjusted_grade', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_grade_id')->references('id')->on('student_grades')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_appeals');
        Schema::dropIfExists('student_grades');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('assessment_types');
        Schema::dropIfExists('excuse_requests');
        Schema::dropIfExists('attendance_records');
    }
};

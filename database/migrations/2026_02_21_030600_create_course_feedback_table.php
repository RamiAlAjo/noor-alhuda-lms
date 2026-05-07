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
        Schema::create('course_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained('course_sections')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('semester_id')->nullable();
            // Ratings (1-5 scale)
            $table->tinyInteger('overall_rating')->nullable();
            $table->tinyInteger('content_quality')->nullable();
            $table->tinyInteger('instructor_knowledge')->nullable();
            $table->tinyInteger('instructor_communication')->nullable();
            $table->tinyInteger('course_organization')->nullable();
            $table->tinyInteger('materials_quality')->nullable();
            $table->tinyInteger('workload_appropriateness')->nullable();
            // Written feedback
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            $table->text('additional_comments')->nullable();
            // Anonymity and status
            $table->boolean('is_anonymous')->default(true);
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_section_id', 'is_submitted']);
            $table->index(['course_section_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_feedback');
    }
};

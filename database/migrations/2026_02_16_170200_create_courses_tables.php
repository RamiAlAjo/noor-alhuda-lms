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
        // Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('major_id')->nullable()->constrained()->onDelete('set null');
            $table->string('code');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->integer('credits')->default(3);
            $table->integer('theory_hours')->default(3);
            $table->integer('lab_hours')->default(0);
            $table->integer('year_level')->nullable(); // 1, 2, 3, 4
            $table->enum('semester_available', ['first', 'second', 'summer', 'both'])->default('both');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Course Sections
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('section_number');
            $table->integer('max_students')->default(30);
            $table->integer('current_students')->default(0);
            $table->string('room')->nullable();
            $table->string('schedule')->nullable(); // JSON for schedule
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['course_id', 'semester_id', 'section_number']);
        });

        // Course Materials
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained('course_sections')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->enum('material_type', ['lecture', 'assignment', 'exam', 'resource', 'other'])->default('lecture');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // Course Teachers (many teachers can teach a section)
        Schema::create('course_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained('course_sections')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['course_section_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_teachers');
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('courses');
    }
};

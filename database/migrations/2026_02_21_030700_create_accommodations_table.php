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
        // Accommodation Types - defines different types of accommodations
        Schema::create('accommodation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('category'); // timing, format, environment, materials, etc.
            $table->json('default_settings')->nullable(); // default values for the accommodation
            $table->boolean('requires_documentation')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Student Accommodations - assigns accommodations to students
        Schema::create('student_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('accommodation_type_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->json('custom_settings')->nullable(); // override default settings
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // active, expired, suspended
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('documentation_path')->nullable(); // path to supporting documents
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
            $table->index(['accommodation_type_id', 'status']);
        });

        // Quiz Accommodations - applies accommodations to specific quizzes
        Schema::create('quiz_accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_accommodation_id')->constrained()->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->integer('extended_time_minutes')->nullable(); // additional time in minutes
            $table->decimal('extended_time_percentage', 5, 2)->nullable(); // or percentage extension
            $table->integer('additional_attempts')->default(0);
            $table->boolean('allow_breaks')->default(false);
            $table->text('special_instructions')->nullable();
            $table->boolean('is_applied')->default(false);
            $table->timestamp('applied_at')->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['student_accommodation_id', 'assessment_id'], 'quiz_accmd_stud_assess_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_accommodations');
        Schema::dropIfExists('student_accommodations');
        Schema::dropIfExists('accommodation_types');
    }
};

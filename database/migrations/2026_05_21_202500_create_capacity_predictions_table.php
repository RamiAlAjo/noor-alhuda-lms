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
        Schema::create('capacity_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->string('prediction_horizon')->default('semester_start'); // e.g. semester_start, mid_semester, pre_registration

            $table->unsignedInteger('predicted_students')->nullable();
            $table->unsignedInteger('recommended_capacity')->nullable();
            $table->unsignedInteger('minimum_viable')->nullable();
            $table->unsignedInteger('maximum_optimal')->nullable();
            $table->decimal('confidence_level', 5, 4)->nullable(); // 0.0000 - 1.0000

            $table->json('feature_importance')->nullable();

            $table->timestamps();

            // Prevent duplicate predictions for the same course/semester/horizon
            $table->unique(['course_id', 'semester_id', 'prediction_horizon'], 'unique_course_semester_horizon');

            // Helpful indexes
            $table->index(['semester_id', 'course_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capacity_predictions');
    }
};

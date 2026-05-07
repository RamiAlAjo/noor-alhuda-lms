<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if competencies table already exists
        if (Schema::hasTable('competencies')) {
            return;
        }

        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            if (Schema::hasTable('departments')) {
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            }
        });

        // Check if course_competencies table already exists
        if (Schema::hasTable('course_competencies')) {
            return;
        }

        // Course-Competency pivot table
        Schema::create('course_competencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_offering_id');
            $table->unsignedBigInteger('competency_id');
            $table->timestamps();

            // Add foreign keys only if the referenced tables exist
            if (Schema::hasTable('course_offerings')) {
                $table->foreign('course_offering_id')->references('id')->on('course_offerings')->onDelete('cascade');
            }

            if (Schema::hasTable('competencies')) {
                $table->foreign('competency_id')->references('id')->on('competencies')->onDelete('cascade');
            }

            $table->unique(['course_offering_id', 'competency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_competencies');
        Schema::dropIfExists('competencies');
    }
};

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
        // Rename course_section_id to course_offering_id in course_teachers table
        Schema::table('course_teachers', function (Blueprint $table) {
            if (Schema::hasColumn('course_teachers', 'course_section_id')) {
                $table->renameColumn('course_section_id', 'course_offering_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_teachers', function (Blueprint $table) {
            if (Schema::hasColumn('course_teachers', 'course_offering_id')) {
                $table->renameColumn('course_offering_id', 'course_section_id');
            }
        });
    }
};

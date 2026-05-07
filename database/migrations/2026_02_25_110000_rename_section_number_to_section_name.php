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
        // Rename section_number to section_name in course_offerings table
        // The original course_sections table had section_number, but the model expects section_name
        Schema::table('course_offerings', function (Blueprint $table) {
            if (Schema::hasColumn('course_offerings', 'section_number')) {
                $table->renameColumn('section_number', 'section_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            if (Schema::hasColumn('course_offerings', 'section_name')) {
                $table->renameColumn('section_name', 'section_number');
            }
        });
    }
};

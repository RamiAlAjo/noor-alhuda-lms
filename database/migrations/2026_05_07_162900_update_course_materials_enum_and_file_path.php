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
        Schema::table('course_materials', function (Blueprint $table) {
            // Update enum to include 'video'
            $table->enum('material_type', ['lecture', 'assignment', 'exam', 'resource', 'other', 'video'])->default('lecture')->change();
            // Make file_path nullable for video materials
            $table->string('file_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            // Revert enum
            $table->enum('material_type', ['lecture', 'assignment', 'exam', 'resource', 'other'])->default('lecture')->change();
            // Revert file_path to not nullable
            $table->string('file_path')->nullable(false)->change();
        });
    }
};
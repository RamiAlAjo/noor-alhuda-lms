<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL ENUMs need to be altered with raw SQL for safety
        DB::statement("ALTER TABLE assessments MODIFY quiz_type ENUM('none', 'quiz', 'pre_quiz', 'post_quiz') NOT NULL DEFAULT 'none'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE assessments MODIFY quiz_type ENUM('none', 'pre_quiz', 'post_quiz') NOT NULL DEFAULT 'none'");
    }
};

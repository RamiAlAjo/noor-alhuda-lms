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
        // Add locking fields to student_grades table
        Schema::table('student_grades', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('notes');
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete()->after('is_locked');
            $table->timestamp('locked_at')->nullable()->after('locked_by');
            $table->text('lock_reason')->nullable()->after('locked_at');
        });

        // Add locking fields to assessments table
        Schema::table('assessments', function (Blueprint $table) {
            $table->boolean('grades_locked')->default(false)->after('is_published');
            $table->foreignId('grades_locked_by')->nullable()->constrained('users')->nullOnDelete()->after('grades_locked');
            $table->timestamp('grades_locked_at')->nullable()->after('grades_locked_by');
        });

        // Create grade lock history table
        Schema::create('grade_lock_history', function (Blueprint $table) {
            $table->id();
            $table->morphs('lockable'); // Can be student_grades or assessments
            $table->boolean('locked')->default(true);
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['lockable_id', 'lockable_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_lock_history');

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['grades_locked', 'grades_locked_by', 'grades_locked_at']);
        });

        Schema::table('student_grades', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'locked_by', 'locked_at', 'lock_reason']);
        });
    }
};

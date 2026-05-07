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
        Schema::table('grade_appeals', function (Blueprint $table) {
            // Add soft deletes if not exists
            if (! Schema::hasColumn('grade_appeals', 'deleted_at')) {
                $table->softDeletes();
            }
            // Add missing columns that the GradeAppeal model expects
            if (! Schema::hasColumn('grade_appeals', 'enrollment_id')) {
                $table->unsignedBigInteger('enrollment_id')->nullable()->after('student_id');
            }
            if (! Schema::hasColumn('grade_appeals', 'assessment_id')) {
                $table->unsignedBigInteger('assessment_id')->nullable()->after('enrollment_id');
            }
            // Rename student_grade_id to grade_id for consistency with the model
            if (Schema::hasColumn('grade_appeals', 'student_grade_id') && ! Schema::hasColumn('grade_appeals', 'grade_id')) {
                $table->renameColumn('student_grade_id', 'grade_id');
            }
            // Add missing columns from the model
            if (! Schema::hasColumn('grade_appeals', 'subject')) {
                $table->string('subject')->nullable()->after('grade_id');
            }
            if (! Schema::hasColumn('grade_appeals', 'description')) {
                $table->text('description')->nullable()->after('subject');
            }
            if (! Schema::hasColumn('grade_appeals', 'student_justification')) {
                $table->text('student_justification')->nullable()->after('description');
            }
            if (! Schema::hasColumn('grade_appeals', 'current_grade')) {
                $table->decimal('current_grade', 5, 2)->nullable()->after('student_justification');
            }
            if (! Schema::hasColumn('grade_appeals', 'requested_grade')) {
                $table->decimal('requested_grade', 5, 2)->nullable()->after('current_grade');
            }
            if (! Schema::hasColumn('grade_appeals', 'teacher_response')) {
                $table->text('teacher_response')->nullable()->after('reviewed_at');
            }
            if (! Schema::hasColumn('grade_appeals', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('teacher_response');
            }
            if (! Schema::hasColumn('grade_appeals', 'escalated_to')) {
                $table->unsignedBigInteger('escalated_to')->nullable()->after('admin_notes');
            }
            if (! Schema::hasColumn('grade_appeals', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable()->after('escalated_to');
            }
            if (! Schema::hasColumn('grade_appeals', 'attachments')) {
                $table->json('attachments')->nullable()->after('escalated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_appeals', function (Blueprint $table) {
            $table->dropColumn([
                'enrollment_id',
                'assessment_id',
                'subject',
                'description',
                'student_justification',
                'current_grade',
                'requested_grade',
                'teacher_response',
                'admin_notes',
                'escalated_to',
                'escalated_at',
                'attachments',
            ]);
        });
    }
};

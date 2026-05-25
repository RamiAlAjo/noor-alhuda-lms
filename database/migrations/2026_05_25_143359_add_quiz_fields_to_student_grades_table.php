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
        Schema::table('student_grades', function (Blueprint $table) {
            if (!Schema::hasColumn('student_grades', 'percentage')) {
                $table->decimal('percentage', 5, 2)->nullable()->after('grade');
            }
            if (!Schema::hasColumn('student_grades', 'passed')) {
                $table->boolean('passed')->default(false)->after('percentage');
            }
            if (!Schema::hasColumn('student_grades', 'max_grade')) {
                $table->decimal('max_grade', 5, 2)->nullable()->after('passed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_grades', function (Blueprint $table) {
            $table->dropColumn(['percentage', 'passed', 'max_grade']);
        });
    }
};

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
        Schema::table('semesters', function (Blueprint $table) {
            $table->date('enrollment_start_date')->nullable()->after('end_date');
            $table->date('enrollment_end_date')->nullable()->after('enrollment_start_date');
            $table->date('drop_start_date')->nullable()->after('enrollment_end_date');
            $table->date('drop_end_date')->nullable()->after('drop_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->dropColumn([
                'enrollment_start_date',
                'enrollment_end_date',
                'drop_start_date',
                'drop_end_date',
            ]);
        });
    }
};

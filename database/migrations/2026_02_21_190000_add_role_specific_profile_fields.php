<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds role-specific fields to user_profiles table for Teachers and Students.
     */
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Teacher-specific fields
            $table->string('cv')->nullable()->after('bio');
            $table->foreignId('department_id')->nullable()->constrained()->after('cv');
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->after('department_id');
            $table->integer('years_of_experience')->nullable()->after('blood_type');
            $table->json('office_hours')->nullable()->after('years_of_experience');

            // Student-specific fields (reusing blood_type for students too)
            $table->foreignId('major_id')->nullable()->constrained()->after('office_hours');
            $table->string('emergency_phone')->nullable()->after('major_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['major_id']);
            $table->dropColumn([
                'cv',
                'department_id',
                'blood_type',
                'years_of_experience',
                'office_hours',
                'major_id',
                'emergency_phone',
            ]);
        });
    }
};

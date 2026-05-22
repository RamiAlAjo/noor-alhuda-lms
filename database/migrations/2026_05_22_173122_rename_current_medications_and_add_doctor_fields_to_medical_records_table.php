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
        Schema::table('medical_records', function (Blueprint $table) {
            // Rename current_medications to medications for consistency with forms and model
            $table->renameColumn('current_medications', 'medications');

            // Add missing doctor fields that the admin form and model expect
            $table->string('doctor_name')->nullable()->after('emergency_contact_relation');
            $table->string('doctor_phone')->nullable()->after('doctor_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->renameColumn('medications', 'current_medications');
            $table->dropColumn(['doctor_name', 'doctor_phone']);
        });
    }
};

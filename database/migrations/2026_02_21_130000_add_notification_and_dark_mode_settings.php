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
        Schema::table('user_settings', function (Blueprint $table) {
            $table->boolean('notification_sound')->default(true)->after('dark_gradient');
            $table->boolean('dark_mode')->default(false)->after('notification_sound');
            $table->boolean('system_theme_detection')->default(true)->after('dark_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn(['notification_sound', 'dark_mode', 'system_theme_detection']);
        });
    }
};

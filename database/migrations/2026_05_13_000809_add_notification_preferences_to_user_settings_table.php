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
            $table->boolean('notification_email')->default(true)->after('notification_sound');
            $table->boolean('notification_push')->default(true)->after('notification_email');
            $table->boolean('notification_grades')->default(true)->after('notification_push');
            $table->boolean('notification_enrollment')->default(true)->after('notification_grades');
            $table->boolean('notification_payments')->default(true)->after('notification_enrollment');
            $table->boolean('notification_announcements')->default(true)->after('notification_payments');
            $table->boolean('notification_reminders')->default(true)->after('notification_announcements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn([
                'notification_email',
                'notification_push',
                'notification_grades',
                'notification_enrollment',
                'notification_payments',
                'notification_announcements',
                'notification_reminders',
            ]);
        });
    }
};

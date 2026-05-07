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
        // Add missing columns to user_settings
        Schema::table('user_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('user_settings', 'notification_sound')) {
                $table->boolean('notification_sound')->default(true)->after('dark_gradient');
            }
            if (! Schema::hasColumn('user_settings', 'dark_mode')) {
                $table->boolean('dark_mode')->default(false)->after('notification_sound');
            }
            if (! Schema::hasColumn('user_settings', 'system_theme_detection')) {
                $table->boolean('system_theme_detection')->default(true)->after('dark_mode');
            }
            if (! Schema::hasColumn('user_settings', 'accent_color')) {
                $table->string('accent_color')->default('blue')->after('locale');
            }
        });

        // Create quiz_attempts table if it doesn't exist
        if (! Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
                $table->integer('attempt_number')->default(1);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->decimal('score', 5, 2)->nullable();
                $table->decimal('percentage', 5, 2)->nullable();
                $table->boolean('passed')->nullable()->default(null);
                $table->text('answers_json')->nullable();
                $table->timestamps();
            });
        }

        // Add attempt_id to student_answers if it doesn't exist
        Schema::table('student_answers', function (Blueprint $table) {
            if (! Schema::hasColumn('student_answers', 'attempt_id')) {
                $table->foreignId('attempt_id')->nullable()->constrained('quiz_attempts')->onDelete('set null')->after('assessment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn(['notification_sound', 'dark_mode', 'system_theme_detection', 'accent_color']);
        });

        Schema::dropIfExists('quiz_attempts');

        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropForeign(['attempt_id']);
            $table->dropColumn(['attempt_id']);
        });
    }
};

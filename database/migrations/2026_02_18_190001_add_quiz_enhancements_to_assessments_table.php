<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Pre/Post Quiz identifier
            $table->enum('quiz_type', ['none', 'pre_quiz', 'post_quiz'])->default('none')->after('weight');

            // Time limit enhancements
            $table->integer('time_limit_minutes')->nullable()->after('quiz_type'); // Time limit in minutes (0 = no limit)
            $table->integer('time_limit_seconds')->default(0)->after('time_limit_minutes'); // Additional seconds
            $table->boolean('show_results_immediately')->default(true)->after('time_limit_seconds');
            $table->boolean('shuffle_questions')->default(false)->after('show_results_immediately');
            $table->boolean('shuffle_options')->default(false)->after('shuffle_questions');

            // Attempts
            $table->integer('attempts_allowed')->nullable()->after('shuffle_options'); // null = unlimited
            $table->decimal('passing_score', 5, 2)->nullable()->after('attempts_allowed'); // Percentage

            // Quiz visibility settings
            $table->timestamp('available_from')->nullable()->after('passing_score');
            $table->timestamp('available_until')->nullable()->after('available_from');

            // Feedback settings
            $table->boolean('show_correct_answers')->default(true)->after('available_until');
            $table->boolean('show_feedback')->default(true)->after('show_correct_answers');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn([
                'quiz_type',
                'time_limit_minutes',
                'time_limit_seconds',
                'show_results_immediately',
                'shuffle_questions',
                'shuffle_options',
                'attempts_allowed',
                'passing_score',
                'available_from',
                'available_until',
                'show_correct_answers',
                'show_feedback',
            ]);
        });
    }
};

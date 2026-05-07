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
        // Notes
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('color')->default('#3b82f6'); // hex color
            $table->boolean('is_pinned')->default(false);
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections')->onDelete('set null');
            $table->timestamps();
        });

        // Calendar Events
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->time('start_time')->nullable();
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->string('color')->default('#3b82f6');
            $table->string('location')->nullable();
            $table->enum('event_type', ['personal', 'exam', 'assignment', 'class', 'meeting', 'other'])->default('personal');
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections')->onDelete('set null');
            $table->boolean('reminder_enabled')->default(false);
            $table->integer('reminder_minutes')->default(30);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_rule')->nullable(); // iCal format
            $table->timestamps();
        });

        // Tasks/Todos
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('priority')->default(2); // 1=low, 2=medium, 3=high
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            $table->foreignId('course_section_id')->nullable()->constrained('course_sections')->onDelete('set null');
            $table->foreignId('reminder_id')->nullable()->constrained('calendar_events')->onDelete('set null');
            $table->timestamps();
        });

        // Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // login, logout, create, update, delete, etc.
            $table->string('entity_type')->nullable(); // User, Course, Enrollment, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('calendar_events');
        Schema::dropIfExists('notes');
    }
};

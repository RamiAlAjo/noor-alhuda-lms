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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('type')->default('notification'); // notification, email, sms
            $table->string('category'); // grade, enrollment, payment, etc.
            $table->string('subject')->nullable(); // For email templates
            $table->text('content');
            $table->text('email_content')->nullable(); // HTML email content
            $table->json('variables')->nullable(); // Available variables for template
            $table->boolean('is_active')->default(true);
            $table->boolean('send_email')->default(false);
            $table->boolean('send_push')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};

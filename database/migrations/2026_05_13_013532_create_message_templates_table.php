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
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject')->nullable();
            $table->text('content');
            $table->enum('category', ['general', 'academic', 'administrative', 'system', 'welcome'])->default('general');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('usage_count')->default(0);
            $table->json('variables')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['is_public', 'usage_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};

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
        Schema::table('messages', function (Blueprint $table) {
            // Conversation support
            if (!Schema::hasColumn('messages', 'conversation_id')) {
                $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade')->after('id');
            }
            if (!Schema::hasColumn('messages', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->constrained('messages')->onDelete('cascade')->after('conversation_id');
            }

            // Enhanced message types and priorities
            if (!Schema::hasColumn('messages', 'message_type')) {
                $table->enum('message_type', ['text', 'system', 'template', 'scheduled', 'bulk'])->default('text')->after('content');
            }
            if (!Schema::hasColumn('messages', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('message_type');
            }

            // Advanced features
            if (!Schema::hasColumn('messages', 'is_starred')) {
                $table->boolean('is_starred')->default(false)->after('is_read');
            }
            if (!Schema::hasColumn('messages', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('is_starred');
            }
            if (!Schema::hasColumn('messages', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false)->after('is_archived');
            }
            if (!Schema::hasColumn('messages', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->after('is_deleted');
            }

            // Scheduling and expiration
            if (!Schema::hasColumn('messages', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('created_at');
            }
            if (!Schema::hasColumn('messages', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('scheduled_at');
            }
            if (!Schema::hasColumn('messages', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('sent_at');
            }

            // Enhanced content
            if (!Schema::hasColumn('messages', 'metadata')) {
                $table->json('metadata')->nullable()->after('content');
            }
            if (!Schema::hasColumn('messages', 'attachments')) {
                $table->json('attachments')->nullable()->after('metadata');
            }
            if (!Schema::hasColumn('messages', 'tags')) {
                $table->json('tags')->nullable()->after('attachments');
            }
            if (!Schema::hasColumn('messages', 'reactions')) {
                $table->json('reactions')->nullable()->after('tags');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'conversation_id', 'parent_id', 'message_type', 'priority',
                'read_at', 'is_starred', 'is_archived', 'is_deleted', 'deleted_at',
                'scheduled_at', 'sent_at', 'expires_at', 'metadata', 'attachments',
                'tags', 'reactions'
            ]);
        });
    }
};

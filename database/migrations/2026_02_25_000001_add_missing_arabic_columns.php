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
        // Add missing Arabic columns to discussion_forums
        Schema::table('discussion_forums', function (Blueprint $table) {
            if (! Schema::hasColumn('discussion_forums', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('title');
            }
            if (! Schema::hasColumn('discussion_forums', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description');
            }
            if (! Schema::hasColumn('discussion_forums', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussion_forums', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'description_ar', 'is_active']);
        });
    }
};

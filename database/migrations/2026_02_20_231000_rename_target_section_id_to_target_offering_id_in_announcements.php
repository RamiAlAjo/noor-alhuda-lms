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
        // Check if the old column exists and new column doesn't
        if (Schema::hasColumn('announcements', 'target_section_id') && ! Schema::hasColumn('announcements', 'target_offering_id')) {
            // Drop foreign key if exists (MySQL/MariaDB)
            try {
                Schema::table('announcements', function (Blueprint $table) {
                    $table->dropForeign(['target_section_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist or database doesn't support it
            }

            // Use Laravel's renameColumn for database-agnostic column renaming
            Schema::table('announcements', function (Blueprint $table) {
                $table->renameColumn('target_section_id', 'target_offering_id');
            });

            // Add the new foreign key (only for databases that support it)
            try {
                Schema::table('announcements', function (Blueprint $table) {
                    $table->foreign('target_offering_id')->references('id')->on('course_offerings')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // SQLite doesn't support adding foreign keys to existing tables
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('announcements', 'target_offering_id') && ! Schema::hasColumn('announcements', 'target_section_id')) {
            // Drop foreign key if exists
            try {
                Schema::table('announcements', function (Blueprint $table) {
                    $table->dropForeign(['target_offering_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist or database doesn't support it
            }

            Schema::table('announcements', function (Blueprint $table) {
                $table->renameColumn('target_offering_id', 'target_section_id');
            });

            try {
                Schema::table('announcements', function (Blueprint $table) {
                    $table->foreign('target_section_id')->references('id')->on('course_offerings')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // SQLite doesn't support adding foreign keys to existing tables
            }
        }
    }
};

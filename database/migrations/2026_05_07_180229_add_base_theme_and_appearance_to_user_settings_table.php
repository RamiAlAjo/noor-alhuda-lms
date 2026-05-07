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
            if (! Schema::hasColumn('user_settings', 'base_theme')) {
                $table->string('base_theme')->default('default-dark')->after('theme');
            }
            if (! Schema::hasColumn('user_settings', 'appearance')) {
                $table->string('appearance')->default('dark')->after('base_theme');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            if (Schema::hasColumn('user_settings', 'base_theme')) {
                $table->dropColumn('base_theme');
            }
            if (Schema::hasColumn('user_settings', 'appearance')) {
                $table->dropColumn('appearance');
            }
        });
    }
};

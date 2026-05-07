<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $columns = DB::connection()->getSchemaBuilder()->getColumnListing('user_profiles');

        if (! in_array('initial_password', $columns)) {
            Schema::table('user_profiles', function (Blueprint $table) {
                $table->string('initial_password', 255)->nullable()->after('postal_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('initial_password');
        });
    }
};

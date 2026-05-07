<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds additional profile fields for users:
     * - nationality
     * - emergency_contact_name
     * - emergency_contact_relationship
     * - personal_email
     * - social_links (JSON)
     * - Additional address fields (city, country, postal_code)
     */
    public function up(): void
    {
        $columns = DB::connection()->getSchemaBuilder()->getColumnListing('user_profiles');

        Schema::table('user_profiles', function (Blueprint $table) use ($columns) {
            // Nationality
            if (! in_array('nationality', $columns)) {
                $table->string('nationality')->nullable()->after('gender');
            }

            // Emergency Contact
            if (! in_array('emergency_contact_name', $columns)) {
                $table->string('emergency_contact_name')->nullable()->after('emergency_phone');
            }
            if (! in_array('emergency_contact_relationship', $columns)) {
                $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
            }

            // Personal Email (separate from institution email)
            if (! in_array('personal_email', $columns)) {
                $table->string('personal_email')->nullable()->after('photo');
            }

            // Social Media Links (JSON)
            if (! in_array('social_links', $columns)) {
                $table->json('social_links')->nullable()->after('bio');
            }

            // Additional Address Details
            if (! in_array('city', $columns)) {
                $table->string('city')->nullable()->after('address');
            }
            if (! in_array('country', $columns)) {
                $table->string('country')->nullable()->after('city');
            }
            if (! in_array('postal_code', $columns)) {
                $table->string('postal_code')->nullable()->after('country');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'nationality',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'personal_email',
                'social_links',
                'city',
                'country',
                'postal_code',
            ]);
        });
    }
};

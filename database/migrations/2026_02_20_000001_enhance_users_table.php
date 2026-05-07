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
        Schema::table('users', function (Blueprint $table) {
            // Add phone number
            $table->string('phone')->nullable()->after('email');

            // Add avatar/profile picture
            $table->string('avatar')->nullable()->after('phone');

            // Account status
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])
                ->default('active')
                ->after('avatar');

            // Is active boolean (for quick checks)
            $table->boolean('is_active')->default(true)->after('status');

            // Last login tracking
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');

            // Add index for status and is_active for better query performance
            $table->index('status');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['is_active']);
            $table->dropColumn([
                'phone',
                'avatar',
                'status',
                'is_active',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};

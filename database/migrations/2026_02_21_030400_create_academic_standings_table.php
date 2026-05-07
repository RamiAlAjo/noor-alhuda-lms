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
        Schema::create('academic_standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->string('standing'); // good_standing, probation, suspension, dismissal
            $table->string('standing_type')->nullable(); // academic, disciplinary
            $table->decimal('gpa_at_time', 3, 2)->nullable();
            $table->decimal('cumulative_gpa', 3, 2)->nullable();
            $table->integer('credits_attempted')->nullable();
            $table->integer('credits_earned')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('set_at')->nullable();
            $table->json('requirements')->nullable(); // Requirements to return to good standing
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'is_active']);
            $table->index(['standing', 'is_active']);
        });

        // Add academic standing fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('academic_standing')->default('good_standing')->after('status');
            $table->decimal('cumulative_gpa', 3, 2)->nullable()->after('academic_standing');
            $table->integer('total_credits_earned')->default(0)->after('cumulative_gpa');
            $table->integer('total_credits_attempted')->default(0)->after('total_credits_earned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['academic_standing', 'cumulative_gpa', 'total_credits_earned', 'total_credits_attempted']);
        });

        Schema::dropIfExists('academic_standings');
    }
};

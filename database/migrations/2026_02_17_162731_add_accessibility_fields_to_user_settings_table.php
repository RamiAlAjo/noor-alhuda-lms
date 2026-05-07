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
            $table->string('background_color')->default('#ffffff')->after('line_spacing');
            $table->string('text_color')->default('#1f2937')->after('background_color');
            $table->string('font_face')->default('sans-serif')->after('text_color');
            $table->integer('font_size')->default(16)->after('font_face');
            $table->string('font_kerning')->default('normal')->after('font_size');
            $table->string('letter_spacing')->default('normal')->after('font_kerning');
            $table->boolean('image_visibility')->default(true)->after('letter_spacing');
            $table->string('link_highlight')->default('none')->after('image_visibility');
            $table->string('text_alignment')->default('left')->after('link_highlight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn([
                'background_color',
                'text_color',
                'font_face',
                'font_size',
                'font_kerning',
                'letter_spacing',
                'image_visibility',
                'link_highlight',
                'text_alignment',
            ]);
        });
    }
};

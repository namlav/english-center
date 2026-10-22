<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('catalog_key', 50)->nullable()->unique();
            $table->string('intro_video_url', 500)->nullable();
            $table->string('intro_video_path', 255)->nullable();
            $table->text('audience')->nullable();
            $table->json('curriculum_outline')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropUnique(['catalog_key']);
            $table->dropColumn(['catalog_key', 'intro_video_url', 'intro_video_path', 'audience', 'curriculum_outline']);
        });
    }
};

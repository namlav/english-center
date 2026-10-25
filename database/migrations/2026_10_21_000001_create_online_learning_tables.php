<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite' && version_compare(DB::connection()->getServerVersion(), '3.35', '<')) {
            throw new RuntimeException('SQLite 3.35+ is required to preserve existing course triggers during schema changes.');
        }

        Schema::create('learning_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // An inline nullable FK avoids SQLite's table rebuild, which would discard existing triggers.
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('ALTER TABLE courses ADD COLUMN learning_category_id INTEGER REFERENCES learning_categories(id) ON DELETE RESTRICT');
        }

        Schema::table('courses', function (Blueprint $table): void {
            if (DB::getDriverName() !== 'sqlite') {
                $table->foreignId('learning_category_id')->nullable()->constrained()->restrictOnDelete();
            }
            $table->string('slug')->nullable()->unique();
            $table->enum('delivery_mode', ['classroom', 'online'])->default('classroom');
            $table->text('summary')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->unsignedInteger('access_days')->default(365);
            $table->timestamp('published_at')->nullable();
            $table->index(['delivery_mode', 'is_active', 'published_at'], 'courses_catalog_idx');
        });

        Schema::create('course_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->unique(['course_id', 'position']);
        });

        Schema::create('lessons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->enum('type', ['text', 'video', 'audio'])->default('text');
            $table->longText('content')->nullable();
            $table->string('media_path')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedInteger('position');
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->unique(['course_section_id', 'slug']);
            $table->unique(['course_section_id', 'position']);
        });

        Schema::create('course_enrollments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->enum('source', ['free', 'manual', 'purchase'])->default('manual');
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->timestamp('started_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
            $table->index(['user_id', 'status', 'expires_at'], 'course_access_idx');
        });

        Schema::create('lesson_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('lesson_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('position_seconds')->default(0);
            $table->timestamp('last_accessed_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
            $table->index(['user_id', 'last_accessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_sections');
        Schema::table('courses', function (Blueprint $table): void {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['learning_category_id']);
            }
            $table->dropIndex('courses_catalog_idx');
            $table->dropUnique(['slug']);
            $table->dropColumn(['learning_category_id', 'slug', 'delivery_mode', 'summary', 'thumbnail_path', 'price', 'sale_price', 'access_days', 'published_at']);
        });
        Schema::dropIfExists('learning_categories');
    }
};

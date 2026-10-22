<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_collections', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Keep the existing SQLite exam validation triggers intact.
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('ALTER TABLE exam_papers ADD COLUMN exam_collection_id INTEGER REFERENCES exam_collections(id) ON DELETE RESTRICT');
        }
        Schema::table('exam_papers', function (Blueprint $table): void {
            if (DB::getDriverName() !== 'sqlite') {
                $table->foreignId('exam_collection_id')->nullable()->constrained()->restrictOnDelete();
            }
            $table->index('exam_collection_id');
            $table->index(['status', 'published_at'], 'exam_latest_idx');
        });

        Schema::create('exam_tags', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('kind', ['question_type', 'topic', 'level'])->default('question_type');
            $table->timestamps();
        });

        Schema::create('exam_section_tags', function (Blueprint $table): void {
            $table->foreignId('exam_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_tag_id')->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->primary(['exam_section_id', 'exam_tag_id']);
        });

        Schema::create('lesson_exam_papers', function (Blueprint $table): void {
            $table->foreignId('lesson_id')->constrained()->restrictOnDelete();
            $table->foreignId('exam_paper_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('position');
            $table->boolean('is_required')->default(false);
            $table->timestamps();
            $table->primary(['lesson_id', 'exam_paper_id']);
            $table->unique(['lesson_id', 'position']);
        });

        Schema::table('question_groups', function (Blueprint $table): void {
            $table->string('response_format', 50)->nullable();
            $table->unsignedSmallInteger('answer_word_limit')->nullable();
            $table->json('rendering_data')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('question_groups', function (Blueprint $table): void {
            $table->dropColumn(['response_format', 'answer_word_limit', 'rendering_data']);
        });
        foreach (['lesson_exam_papers', 'exam_section_tags', 'exam_tags'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('exam_papers', function (Blueprint $table): void {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['exam_collection_id']);
            }
            $table->dropIndex(['exam_collection_id']);
            $table->dropIndex('exam_latest_idx');
            $table->dropColumn('exam_collection_id');
        });
        Schema::dropIfExists('exam_collections');
    }
};

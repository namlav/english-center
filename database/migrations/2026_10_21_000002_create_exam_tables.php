<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_papers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('learning_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('skill', ['listening', 'reading', 'writing', 'speaking', 'mixed']);
            $table->unsignedInteger('duration_seconds');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'learning_category_id', 'skill'], 'exam_catalog_idx');
        });

        Schema::create('exam_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_paper_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->enum('skill', ['listening', 'reading', 'writing', 'speaking']);
            $table->unsignedInteger('position');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->longText('instructions')->nullable();
            $table->timestamps();
            $table->unique(['exam_paper_id', 'position']);
            $table->unique(['id', 'exam_paper_id'], 'exam_section_paper_unique');
        });

        Schema::create('question_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_section_id')->constrained()->restrictOnDelete();
            $table->string('title')->nullable();
            $table->unsignedInteger('position');
            $table->longText('instructions')->nullable();
            $table->longText('passage')->nullable();
            $table->text('transcript')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->unique(['exam_section_id', 'position']);
            $table->unique(['id', 'exam_section_id'], 'question_group_section_unique');
        });

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('question_group_id');
            $table->unsignedBigInteger('exam_section_id');
            $table->unsignedInteger('position');
            $table->longText('prompt');
            $table->enum('type', ['single_choice', 'multiple_choice', 'short_answer', 'true_false', 'essay', 'speaking']);
            $table->decimal('max_points', 8, 2)->default(1);
            $table->timestamps();
            $table->foreign(['question_group_id', 'exam_section_id'], 'question_group_section_fk')
                ->references(['id', 'exam_section_id'])->on('question_groups')->restrictOnDelete();
            $table->unique(['question_group_id', 'position']);
            $table->unique(['id', 'exam_section_id'], 'question_section_unique');
        });

        Schema::create('question_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->constrained()->restrictOnDelete();
            $table->string('label', 16);
            $table->text('content');
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->unique(['question_id', 'label']);
            $table->unique(['question_id', 'position']);
        });

        Schema::create('question_answer_keys', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->unique()->constrained()->restrictOnDelete();
            $table->json('accepted_answers')->nullable();
            $table->boolean('case_sensitive')->default(false);
            $table->json('rubric')->nullable();
            $table->longText('explanation')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('exam_paper_id')->constrained()->restrictOnDelete();
            $table->enum('mode', ['practice', 'full']);
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'abandoned'])->default('in_progress');
            $table->string('title_snapshot');
            $table->timestamp('started_at');
            $table->unsignedInteger('time_limit_seconds')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->unsignedInteger('elapsed_seconds')->default(0);
            $table->decimal('earned_points', 10, 2)->nullable();
            $table->decimal('max_points', 10, 2)->default(0);
            $table->decimal('scaled_score', 7, 2)->nullable();
            $table->timestamps();
            $table->unique(['id', 'exam_paper_id'], 'attempt_paper_unique');
            $table->index(['user_id', 'started_at'], 'attempt_history_idx');
            $table->index(['exam_paper_id', 'status']);
        });

        Schema::create('exam_attempt_sections', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('exam_attempt_id');
            $table->unsignedBigInteger('exam_paper_id');
            $table->unsignedBigInteger('exam_section_id');
            $table->string('title_snapshot');
            $table->longText('instructions_snapshot')->nullable();
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->unique(['exam_attempt_id', 'exam_section_id'], 'attempt_section_unique');
            $table->unique(['exam_attempt_id', 'position'], 'attempt_section_position_unique');
            $table->foreign(['exam_attempt_id', 'exam_paper_id'], 'attempt_section_attempt_fk')
                ->references(['id', 'exam_paper_id'])->on('exam_attempts')->cascadeOnDelete();
            $table->foreign(['exam_section_id', 'exam_paper_id'], 'attempt_section_paper_fk')
                ->references(['id', 'exam_paper_id'])->on('exam_sections')->restrictOnDelete();
        });

        Schema::create('attempt_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('exam_attempt_id');
            $table->unsignedBigInteger('exam_section_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedInteger('position');
            $table->enum('type_snapshot', ['single_choice', 'multiple_choice', 'short_answer', 'true_false', 'essay', 'speaking']);
            $table->longText('prompt_snapshot');
            $table->json('group_snapshot')->nullable();
            $table->json('options_snapshot')->nullable();
            $table->json('grading_snapshot')->nullable();
            $table->json('response')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('awarded_points', 8, 2)->nullable();
            $table->decimal('max_points', 8, 2);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            $table->unique(['exam_attempt_id', 'question_id'], 'attempt_answer_unique');
            $table->unique(['exam_attempt_id', 'position'], 'attempt_answer_position_unique');
            $table->foreign(['exam_attempt_id', 'exam_section_id'], 'answer_attempt_section_fk')
                ->references(['exam_attempt_id', 'exam_section_id'])->on('exam_attempt_sections')->cascadeOnDelete();
            $table->foreign(['question_id', 'exam_section_id'], 'answer_question_section_fk')
                ->references(['id', 'exam_section_id'])->on('questions')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        foreach (['attempt_answers', 'exam_attempt_sections', 'exam_attempts', 'question_answer_keys', 'question_options', 'questions', 'question_groups', 'exam_sections', 'exam_papers'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcard_decks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('visibility', ['private', 'public', 'course'])->default('private');
            $table->timestamps();
            $table->index(['visibility', 'created_at']);
        });

        Schema::create('flashcards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('flashcard_deck_id')->constrained()->restrictOnDelete();
            $table->text('front');
            $table->text('back');
            $table->string('pronunciation')->nullable();
            $table->text('example')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->unique(['flashcard_deck_id', 'position']);
        });

        Schema::create('flashcard_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('flashcard_id')->constrained()->restrictOnDelete();
            $table->decimal('ease_factor', 5, 2)->default(2.50);
            $table->unsignedInteger('interval_days')->default(0);
            $table->unsignedInteger('repetitions')->default(0);
            $table->unsignedTinyInteger('last_rating')->nullable();
            $table->timestamp('due_at');
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'flashcard_id']);
            $table->index(['user_id', 'due_at'], 'flashcard_due_idx');
        });

        Schema::create('course_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('content')->nullable();
            $table->enum('status', ['pending', 'published', 'hidden'])->default('pending');
            $table->timestamps();
            $table->unique(['course_id', 'user_id']);
            $table->index(['course_id', 'status']);
        });

        Schema::create('exam_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_paper_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->text('content');
            $table->enum('status', ['pending', 'published', 'hidden'])->default('pending');
            $table->timestamps();
            $table->index(['exam_paper_id', 'status', 'created_at'], 'exam_comments_feed_idx');
        });
    }

    public function down(): void
    {
        foreach (['exam_comments', 'course_reviews', 'flashcard_reviews', 'flashcards', 'flashcard_decks'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

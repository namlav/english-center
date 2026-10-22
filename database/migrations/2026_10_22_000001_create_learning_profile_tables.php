<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learner_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('occupation', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('course_learning_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->text('learning_reason')->nullable();
            $table->boolean('consultation_opt_in')->default(false);
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('profile_completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
        });

        Schema::create('learning_score_scales', function (Blueprint $table): void {
            $table->string('code', 40)->primary();
            $table->string('label');
            $table->decimal('min_score', 6, 2);
            $table->decimal('max_score', 6, 2);
            $table->decimal('score_step', 6, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['code', 'min_score', 'max_score', 'score_step'], 'score_scale_bounds_unique');
        });

        // Stable codes let the upgrade SQL add reference data without relying on auto-increment IDs.
        DB::table('learning_score_scales')->insert([
            ['code' => 'toeic_lr', 'label' => 'TOEIC Listening & Reading', 'min_score' => 10, 'max_score' => 990, 'score_step' => 5],
            ['code' => 'toeic_speaking', 'label' => 'TOEIC Speaking', 'min_score' => 0, 'max_score' => 200, 'score_step' => 10],
            ['code' => 'toeic_writing', 'label' => 'TOEIC Writing', 'min_score' => 0, 'max_score' => 200, 'score_step' => 10],
            ['code' => 'ielts_band', 'label' => 'IELTS band', 'min_score' => 0, 'max_score' => 9, 'score_step' => 0.5],
        ]);

        Schema::create('course_learning_goals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_learning_profile_id')->constrained()->restrictOnDelete();
            $table->string('score_scale_code', 40);
            $table->decimal('min_score', 6, 2);
            $table->decimal('max_score', 6, 2);
            $table->decimal('score_step', 6, 2);
            $table->decimal('current_score', 6, 2)->nullable();
            $table->decimal('target_score', 6, 2)->nullable();
            $table->date('target_exam_date')->nullable();
            $table->timestamps();
            $table->unique(['course_learning_profile_id', 'score_scale_code'], 'course_goal_scale_unique');
            $table->foreign(['score_scale_code', 'min_score', 'max_score', 'score_step'], 'course_goal_scale_bounds_fk')
                ->references(['code', 'min_score', 'max_score', 'score_step'])->on('learning_score_scales')->restrictOnDelete();
        });

        Schema::create('course_instructors', function (Blueprint $table): void {
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->primary(['course_id', 'user_id']);
            $table->unique(['course_id', 'position']);
        });

        Schema::create('course_skills', function (Blueprint $table): void {
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('skill', ['listening', 'reading', 'speaking', 'writing', 'grammar', 'vocabulary', 'pronunciation']);
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->primary(['course_id', 'skill']);
            $table->unique(['course_id', 'position']);
        });

        Schema::table('courses', function (Blueprint $table): void {
            $table->json('learning_outcomes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table): void {
            $table->dropColumn('learning_outcomes');
        });
        foreach (['course_skills', 'course_instructors', 'course_learning_goals', 'learning_score_scales', 'course_learning_profiles', 'learner_profiles'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

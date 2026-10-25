<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function checks(): array
    {
        return [
            'courses' => "price >= 0 AND (sale_price IS NULL OR (sale_price >= 0 AND sale_price <= price)) AND access_days > 0 AND (delivery_mode <> 'online' OR slug IS NOT NULL)",
            'course_sections' => 'position > 0',
            'lessons' => 'position > 0 AND duration_seconds >= 0',
            'course_enrollments' => '(expires_at IS NULL OR expires_at > started_at) AND (completed_at IS NULL OR completed_at >= started_at)',
            'lesson_progress' => 'position_seconds >= 0',
            'exam_papers' => "duration_seconds > 0 AND (status <> 'published' OR published_at IS NOT NULL)",
            'exam_sections' => 'position > 0 AND (duration_seconds IS NULL OR duration_seconds > 0)',
            'question_groups' => 'position > 0',
            'questions' => 'position > 0 AND max_points > 0',
            'question_options' => 'position > 0',
            'exam_attempts' => "max_points >= 0 AND elapsed_seconds >= 0 AND (time_limit_seconds IS NULL OR time_limit_seconds > 0) AND (mode <> 'full' OR time_limit_seconds IS NOT NULL) AND (earned_points IS NULL OR (earned_points >= 0 AND earned_points <= max_points)) AND (scaled_score IS NULL OR scaled_score >= 0) AND (submitted_at IS NULL OR submitted_at >= started_at) AND (status NOT IN ('submitted', 'graded') OR submitted_at IS NOT NULL) AND (status <> 'graded' OR (graded_at IS NOT NULL AND earned_points IS NOT NULL)) AND (graded_at IS NULL OR (submitted_at IS NOT NULL AND graded_at >= submitted_at))",
            'exam_attempt_sections' => 'position > 0',
            'attempt_answers' => 'position > 0 AND max_points > 0 AND (awarded_points IS NULL OR (awarded_points >= 0 AND awarded_points <= max_points))',
            'flashcard_decks' => "(visibility <> 'course' OR course_id IS NOT NULL) AND (visibility <> 'private' OR user_id IS NOT NULL)",
            'flashcards' => 'position > 0',
            'flashcard_reviews' => 'ease_factor >= 1.3 AND interval_days >= 0 AND repetitions >= 0 AND (last_rating IS NULL OR last_rating BETWEEN 0 AND 5)',
            'course_reviews' => 'rating BETWEEN 1 AND 5',
        ];
    }

    public function up(): void
    {
        foreach ($this->checks() as $table => $expression) {
            if (DB::table($table)->whereRaw('NOT ('.$expression.')')->exists()) {
                throw new RuntimeException('Invalid learning data in '.$table.'; repair it before migrating.');
            }

            if (DB::getDriverName() === 'sqlite') {
                $columns = Schema::getColumnListing($table);
                $newExpression = preg_replace_callback('/\b[a-z_]+\b/', fn (array $match): string => in_array($match[0], $columns, true) ? 'NEW.'.$match[0] : $match[0], $expression);
                foreach (['INSERT', 'UPDATE'] as $event) {
                    DB::unprepared("CREATE TRIGGER {$table}_learning_".strtolower($event)." BEFORE {$event} ON {$table} WHEN NOT ({$newExpression}) BEGIN SELECT RAISE(ABORT, 'Invalid {$table} learning data'); END");
                }
            } else {
                DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_learning_check CHECK ({$expression})");
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->checks()) as $table) {
            if (DB::getDriverName() === 'sqlite') {
                DB::unprepared("DROP TRIGGER IF EXISTS {$table}_learning_insert");
                DB::unprepared("DROP TRIGGER IF EXISTS {$table}_learning_update");
            } elseif (DB::getDriverName() === 'mariadb') {
                DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$table}_learning_check");
            } else {
                DB::statement("ALTER TABLE {$table} DROP CHECK {$table}_learning_check");
            }
        }
    }
};

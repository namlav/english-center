<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function checks(): array
    {
        $validCurrent = '(current_score IS NULL OR (current_score BETWEEN min_score AND max_score AND ROUND(1.0 * (current_score - min_score) / score_step, 0) = 1.0 * (current_score - min_score) / score_step))';
        $validTarget = '(target_score IS NULL OR (target_score BETWEEN min_score AND max_score AND ROUND(1.0 * (target_score - min_score) / score_step, 0) = 1.0 * (target_score - min_score) / score_step))';

        return [
            'learning_score_scales' => 'min_score >= 0 AND max_score >= min_score AND score_step > 0',
            'course_learning_profiles' => '(consultation_opt_in = 0 AND consented_at IS NULL) OR (consultation_opt_in = 1 AND consented_at IS NOT NULL)',
            'course_learning_goals' => 'score_step > 0 AND (current_score IS NOT NULL OR target_score IS NOT NULL) AND '.$validCurrent.' AND '.$validTarget,
            'course_instructors' => 'position > 0',
            'course_skills' => 'position > 0',
            'lesson_exam_papers' => 'position > 0',
            'question_groups' => 'answer_word_limit IS NULL OR answer_word_limit > 0',
        ];
    }

    public function up(): void
    {
        foreach ($this->checks() as $table => $expression) {
            if (DB::table($table)->whereRaw('NOT ('.$expression.')')->exists()) {
                throw new RuntimeException('Invalid profile/catalog data in '.$table.'; repair it before migrating.');
            }
            if (DB::getDriverName() === 'sqlite') {
                $columns = Schema::getColumnListing($table);
                $newExpression = preg_replace_callback('/\b[a-z_]+\b/', fn (array $match): string => in_array($match[0], $columns, true) ? 'NEW.'.$match[0] : $match[0], $expression);
                foreach (['INSERT', 'UPDATE'] as $event) {
                    DB::unprepared("CREATE TRIGGER {$table}_profile_".strtolower($event)." BEFORE {$event} ON {$table} WHEN NOT ({$newExpression}) BEGIN SELECT RAISE(ABORT, 'Invalid {$table} profile data'); END");
                }
            } else {
                DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_profile_check CHECK ({$expression})");
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->checks()) as $table) {
            if (DB::getDriverName() === 'sqlite') {
                DB::unprepared("DROP TRIGGER IF EXISTS {$table}_profile_insert");
                DB::unprepared("DROP TRIGGER IF EXISTS {$table}_profile_update");
            } elseif (DB::getDriverName() === 'mariadb') {
                DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$table}_profile_check");
            } else {
                DB::statement("ALTER TABLE {$table} DROP CHECK {$table}_profile_check");
            }
        }
    }
};

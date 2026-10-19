<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @return array<string, array<string, string>> */
    private function protectedReferences(): array
    {
        return [
            'teachers' => ['user_id' => 'users'],
            'students' => ['user_id' => 'users'],
            'class_rooms' => ['course_id' => 'courses'],
            'enrollments' => ['class_room_id' => 'class_rooms', 'student_id' => 'students'],
            'attendances' => ['class_room_id' => 'class_rooms', 'student_id' => 'students'],
        ];
    }

    /** @return array<string, string> */
    private function checks(): array
    {
        return [
            'courses' => 'duration_hours > 0',
            'class_rooms' => 'tuition_fee >= 0 AND max_students > 0',
            'enrollments' => 'paid_tuition >= 0 AND (midterm_score IS NULL OR midterm_score BETWEEN 0 AND 10) AND (final_score IS NULL OR final_score BETWEEN 0 AND 10)',
        ];
    }

    public function up(): void
    {
        foreach ($this->checks() as $table => $expression) {
            if (DB::table($table)->whereRaw('NOT ('.$expression.')')->exists()) {
                throw new RuntimeException('Invalid existing values in '.$table.'; repair them before migrating.');
            }
        }
        $invalidAttendance = DB::table('attendances as a')->leftJoin('enrollments as e', function (JoinClause $join): void {
            $join->on('a.class_room_id', '=', 'e.class_room_id')->on('a.student_id', '=', 'e.student_id');
        })->whereNull('e.id')->exists();
        if ($invalidAttendance) {
            throw new RuntimeException('An attendance does not belong to an enrolled student; repair it before migrating.');
        }

        foreach ($this->protectedReferences() as $table => $references) {
            Schema::table($table, function (Blueprint $blueprint) use ($references): void {
                foreach ($references as $column => $parent) {
                    $blueprint->dropForeign([$column]);
                    $blueprint->foreign($column)->references('id')->on($parent)->restrictOnDelete();
                }
            });
        }
        Schema::table('attendances', function (Blueprint $table): void {
            $table->foreign(['class_room_id', 'student_id'], 'attendance_enrolled_student_fk')
                ->references(['class_room_id', 'student_id'])->on('enrollments')->restrictOnDelete();
        });
        foreach ($this->checks() as $table => $expression) {
            if (DB::getDriverName() === 'sqlite') {
                $columns = Schema::getColumnListing($table);
                $newExpression = preg_replace_callback('/\b[a-z_]+\b/', fn (array $match): string => in_array($match[0], $columns, true) ? 'NEW.'.$match[0] : $match[0], $expression);
                foreach (['INSERT', 'UPDATE'] as $event) {
                    $name = $table.'_training_'.strtolower($event);
                    DB::unprepared("CREATE TRIGGER {$name} BEFORE {$event} ON {$table} WHEN NOT ({$newExpression}) BEGIN SELECT RAISE(ABORT, 'Invalid {$table} values'); END");
                }
            } else {
                DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_training_check CHECK ({$expression})");
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->checks()) as $table) {
            if (DB::getDriverName() === 'sqlite') {
                DB::unprepared("DROP TRIGGER IF EXISTS {$table}_training_insert");
                DB::unprepared("DROP TRIGGER IF EXISTS {$table}_training_update");
            } elseif (DB::getDriverName() === 'mariadb') {
                DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$table}_training_check");
            } else {
                DB::statement("ALTER TABLE {$table} DROP CHECK {$table}_training_check");
            }
        }
        Schema::table('attendances', function (Blueprint $table): void {
            $table->dropForeign('attendance_enrolled_student_fk');
        });
        foreach ($this->protectedReferences() as $table => $references) {
            Schema::table($table, function (Blueprint $blueprint) use ($references): void {
                foreach ($references as $column => $parent) {
                    $blueprint->dropForeign([$column]);
                    $blueprint->foreign($column)->references('id')->on($parent)->cascadeOnDelete();
                }
            });
        }
    }
};

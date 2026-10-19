<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class AuditTrainingData extends Command
{
    protected $signature = 'data:audit {--json : Print a JSON report}';

    protected $description = 'Kiểm tra dữ liệu theo cấu trúc chung của nhóm (chỉ đọc).';

    public function handle(): int
    {
        $counts = [];
        foreach (['users', 'teachers', 'students', 'courses', 'class_rooms', 'enrollments', 'attendances'] as $table) {
            $counts[$table] = DB::table($table)->count();
        }
        $issues = [];
        foreach (['teachers' => 'teacher', 'students' => 'student'] as $table => $role) {
            $bad = DB::table($table)->leftJoin('users', 'users.id', '=', $table.'.user_id')->where(function (Builder $query) use ($role): void {
                $query->whereNull('users.id')->orWhere('users.role', '!=', $role);
            })->count();
            if ($bad > 0) {
                $issues[] = $table.': '.$bad.' hồ sơ không khớp vai trò tài khoản.';
            }
        }
        $fullClasses = DB::table('class_rooms as c')->select('c.id', 'c.max_students')->selectSub(DB::table('enrollments as e')->selectRaw('COUNT(*)')->whereColumn('e.class_room_id', 'c.id'), 'student_count')->get();
        foreach ($fullClasses as $classRoom) {
            if ($classRoom->student_count > $classRoom->max_students) {
                $issues[] = 'Lớp #'.$classRoom->id.' vượt sĩ số.';
            }
        }
        $unregistered = DB::table('attendances as a')->leftJoin('enrollments as e', function (JoinClause $join): void {
            $join->on('a.class_room_id', '=', 'e.class_room_id')->on('a.student_id', '=', 'e.student_id');
        })->whereNull('e.id')->count();
        if ($unregistered > 0) {
            $issues[] = 'Có '.$unregistered.' điểm danh cho học viên chưa đăng ký lớp.';
        }
        $overpayments = DB::table('enrollments as e')->join('class_rooms as c', 'c.id', '=', 'e.class_room_id')->whereColumn('e.paid_tuition', '>', 'c.tuition_fee')->count();
        if ($overpayments > 0) {
            $issues[] = 'Có '.$overpayments.' đăng ký thu vượt học phí lớp.';
        }
        $report = ['counts' => $counts, 'issues' => $issues];
        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        } else {
            $this->table(['Bảng', 'Số bản ghi'], collect($counts)->map(fn (int $count, string $table): array => [$table, $count])->values()->all());
            foreach ($issues as $issue) {
                $this->error($issue);
            }
            if ($issues === []) {
                $this->info('Dữ liệu nhất quán.');
            }
        }

        return $issues === [] ? self::SUCCESS : self::FAILURE;
    }
}

<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $password = Hash::make('123456');
            $this->account('admin@englishcenter.vn', 'Quản Trị Viên Hệ Thống', 'admin', $password);
            $teachers = [];
            foreach ([['minh', 'ThS. Nguyễn Quang Minh', 'IELTS Academic'], ['lan', 'Cô Trần Phương Lan', 'TOEIC và giao tiếp'], ['vy', 'Lê Thảo Vy', 'Tiếng Anh tổng quát']] as $i => [$email, $name, $specialization]) {
                $user = $this->account('gv.'.$email.'@englishcenter.vn', $name, 'teacher', $password);
                $teachers[] = Teacher::firstOrCreate(['teacher_code' => sprintf('GV%03d', $i + 1)], [
                    'user_id' => $user->id, 'specialization' => $specialization, 'degree' => 'Cử nhân Ngôn ngữ Anh',
                ]);
            }
            $students = [];
            $names = ['Lê Hoàng An', 'Phạm Thanh Bình', 'Võ Minh Châu', 'Nguyễn An', 'Trần Chi', 'Lê Dũng', 'Phạm Hà', 'Hoàng Hân', 'Võ Huy', 'Đặng Khánh', 'Bùi Lan', 'Đỗ Linh', 'Hồ Minh', 'Ngô Nam', 'Dương Ngọc', 'Lý Phúc', 'Mai Quân', 'Tạ Quỳnh', 'Phan Sơn', 'Huỳnh Thảo', 'Cao Thiện', 'Vũ Trang', 'Đinh Tú', 'Trịnh Uyên'];
            foreach ($names as $i => $name) {
                $email = match ($i) {
                    0 => 'hv.an', 1 => 'hv.binh', 2 => 'hv.chau', default => sprintf('hv.demo%02d', $i + 1)
                };
                $user = $this->account($email.'@englishcenter.vn', $name, 'student', $password);
                $students[] = Student::firstOrCreate(['student_code' => sprintf('HV%03d', $i + 1)], [
                    'user_id' => $user->id, 'date_of_birth' => sprintf('2005-%02d-15', $i % 12 + 1), 'gender' => $i % 2 === 0 ? 'male' : 'female', 'address' => 'TP. Hồ Chí Minh',
                ]);
            }
            $courses = [];
            foreach ([['KH-IELTS', 'Luyện thi IELTS Intensive (6.5+)', 'Nâng cao', 60], ['KH-TOEIC', 'Luyện thi TOEIC 650+ Cấp tốc', 'Trung cấp', 45], ['KH-TAGT', 'Tiếng Anh Giao Tiếp Công Sở', 'Cơ bản - Trung cấp', 36]] as [$code, $name, $level, $hours]) {
                $c = Course::firstOrCreate(['code' => $code], ['name' => $name, 'level' => $level, 'duration_hours' => $hours, 'description' => 'Chương trình đào tạo mẫu phục vụ đồ án.', 'is_active' => true]);
                if (\Illuminate\Support\Facades\Schema::hasColumn('courses', 'slug') && empty($c->slug)) {
                    $slugs = ['KH-IELTS' => 'luyen-thi-ielts-intensive-offline', 'KH-TOEIC' => 'luyen-thi-toeic-650-cap-toc-offline', 'KH-TAGT' => 'tieng-anh-giao-tiep-cong-so-offline'];
                    $c->slug = $slugs[$code] ?? '';
                    if (\Illuminate\Support\Facades\Schema::hasColumn('courses', 'price')) {
                        $prices = ['KH-IELTS' => 4500000, 'KH-TOEIC' => 3200000, 'KH-TAGT' => 2800000];
                        $c->price = $prices[$code] ?? 0;
                    }
                    $c->save();
                }
                $courses[] = $c;
            }
            $today = now('Asia/Ho_Chi_Minh')->startOfDay();
            foreach (['ongoing', 'ongoing', 'upcoming', 'completed', 'upcoming', 'cancelled'] as $i => $status) {
                $code = ['IELTS-2601', 'TOEIC-2601', 'TAGT-2601', 'DEMO-CL04', 'DEMO-CL05', 'DEMO-CL06'][$i];
                $start = $today->copy()->addDays(match ($i) {
                    0, 1 => -14, 3 => -70, default => 7
                });
                $classRoom = ClassRoom::firstOrCreate(['code' => $code], [
                    'name' => $courses[$i % 3]->name.' — Lớp '.($i + 1), 'course_id' => $courses[$i % 3]->id,
                    'teacher_id' => $teachers[$i % 3]->id, 'start_date' => $start->toDateString(), 'expected_end_date' => $start->copy()->addMonths(2)->toDateString(), 'schedule' => 'Thứ 2, 4, 6 — 18:00–19:30',
                    'tuition_fee' => [4500000, 2800000, 2200000][$i % 3], 'max_students' => 25, 'status' => $status,
                ]);
                if ($classRoom->status === 'cancelled') {
                    continue;
                }
                for ($j = 0; $j < 8; $j++) {
                    $student = $students[($i * 4 + $j) % 24];
                    $enrollment = Enrollment::firstOrCreate(['class_room_id' => $classRoom->id, 'student_id' => $student->id], [
                        'enrollment_date' => $classRoom->start_date->copy()->subWeek()->toDateString(),
                        'paid_tuition' => $classRoom->tuition_fee,
                        'payment_status' => 'paid',
                        'midterm_score' => $status === 'completed' ? ($j === 0 ? 4 : 7) : null,
                        'final_score' => $status === 'completed' ? ($j === 0 ? 4 : 7.5) : null,
                        'result_status' => $status === 'completed' ? ($j === 0 ? 'failed' : 'passed') : 'studying',
                    ]);
                    if ($enrollment->payment_status !== 'paid' || (float) $enrollment->paid_tuition !== (float) $classRoom->tuition_fee) {
                        $enrollment->update(['paid_tuition' => $classRoom->tuition_fee, 'payment_status' => 'paid']);
                    }
                    foreach ([0, 2, 4] as $k => $offset) {
                        $date = $classRoom->start_date->copy()->addDays($offset);
                        if ($date->gt($today)) {
                            continue;
                        }
                        Attendance::firstOrCreate(['class_room_id' => $classRoom->id, 'student_id' => $student->id, 'attendance_date' => $date->toDateString()], [
                            'status' => match (($j + $k) % 9) {
                                0 => 'absent', 1 => 'late', default => 'present'
                            },
                            'note' => 'Dữ liệu mẫu cho đồ án.',
                        ]);
                    }
                }
            }
        }, 5);
    }

    private function account(string $email, string $name, string $role, string $password): User
    {
        return User::firstOrCreate(['email' => $email], ['name' => $name, 'role' => $role, 'password' => $password, 'is_active' => true]);
    }
}

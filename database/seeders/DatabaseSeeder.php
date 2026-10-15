<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Course;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Attendance;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo tài khoản Admin
        User::create([
            'name' => 'Quản Trị Viên Hệ Thống',
            'email' => 'admin@englishcenter.vn',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'phone' => '0901000001',
            'is_active' => true,
        ]);

        // 2. Tạo 2 Giáo viên mẫu
        $gv1User = User::create([
            'name' => 'ThS. Nguyễn Quang Minh',
            'email' => 'gv.minh@englishcenter.vn',
            'password' => Hash::make('123456'),
            'role' => 'teacher',
            'phone' => '0902000001',
            'is_active' => true,
        ]);
        $gv1 = Teacher::create([
            'user_id' => $gv1User->id,
            'teacher_code' => 'GV001',
            'specialization' => 'IELTS Academic 8.5',
            'degree' => 'Thạc sĩ Lý luận & PPDH Tiếng Anh (TESOL)',
            'bio' => '8 năm kinh nghiệm luyện thi IELTS cấp tốc.',
        ]);

        $gv2User = User::create([
            'name' => 'Cô Trần Phương Lan',
            'email' => 'gv.lan@englishcenter.vn',
            'password' => Hash::make('123456'),
            'role' => 'teacher',
            'phone' => '0902000002',
            'is_active' => true,
        ]);
        $gv2 = Teacher::create([
            'user_id' => $gv2User->id,
            'teacher_code' => 'GV002',
            'specialization' => 'TOEIC 990 & Tiếng Anh Giao Tiếp',
            'degree' => 'Cử nhân Ngôn ngữ Anh ĐH Sư Phạm',
            'bio' => 'Chuyên gia luyện thi TOEIC 4 kỹ năng và phản xạ giao tiếp.',
        ]);

        // 3. Tạo 3 Học viên mẫu
        $studentsData = [
            ['name' => 'Lê Hoàng An', 'email' => 'hv.an@englishcenter.vn', 'code' => 'HV001', 'dob' => '2004-05-12', 'gender' => 'male'],
            ['name' => 'Phạm Thanh Bình', 'email' => 'hv.binh@englishcenter.vn', 'code' => 'HV002', 'dob' => '2005-08-20', 'gender' => 'female'],
            ['name' => 'Võ Minh Châu', 'email' => 'hv.chau@englishcenter.vn', 'code' => 'HV003', 'dob' => '2004-11-03', 'gender' => 'female'],
        ];
        $students = [];
        foreach ($studentsData as $idx => $st) {
            $u = User::create([
                'name' => $st['name'],
                'email' => $st['email'],
                'password' => Hash::make('123456'),
                'role' => 'student',
                'phone' => '090300000' . ($idx + 1),
                'is_active' => true,
            ]);
            $students[] = Student::create([
                'user_id' => $u->id,
                'student_code' => $st['code'],
                'date_of_birth' => $st['dob'],
                'gender' => $st['gender'],
                'address' => 'TP. Hồ Chí Minh',
            ]);
        }

        // 4. Tạo 3 Khóa học mẫu
        $courseIelts = Course::create([
            'code' => 'KH-IELTS',
            'name' => 'Luyện thi IELTS Intensive (6.5+)',
            'level' => 'Nâng cao',
            'duration_hours' => 60,
            'description' => 'Khóa học luyện thi IELTS 4 kỹ năng cam kết đầu ra 6.5+.',
        ]);
        $courseToeic = Course::create([
            'code' => 'KH-TOEIC',
            'name' => 'Luyện thi TOEIC 650+ Cấp tốc',
            'level' => 'Trung cấp',
            'duration_hours' => 45,
            'description' => 'Bứt phá điểm số TOEIC Listening & Reading trong 2 tháng.',
        ]);
        $courseComm = Course::create([
            'code' => 'KH-TAGT',
            'name' => 'Tiếng Anh Giao Tiếp Công Sở',
            'level' => 'Cơ bản - Trung cấp',
            'duration_hours' => 36,
            'description' => 'Thực hành phản xạ giao tiếp và thuyết trình bằng tiếng Anh.',
        ]);

        // 5. Tạo 3 Lớp học mẫu
        $class1 = ClassRoom::create([
            'code' => 'IELTS-2601',
            'name' => 'IELTS Intensive K01 - Tối 2-4-6',
            'course_id' => $courseIelts->id,
            'teacher_id' => $gv1->id,
            'start_date' => '2026-10-15',
            'schedule' => 'Thứ 2 - 4 - 6 (18:00 - 20:00)',
            'tuition_fee' => 4500000,
            'max_students' => 20,
            'status' => 'ongoing',
        ]);
        $class2 = ClassRoom::create([
            'code' => 'TOEIC-2601',
            'name' => 'TOEIC 650+ K01 - Tối 3-5-7',
            'course_id' => $courseToeic->id,
            'teacher_id' => $gv2->id,
            'start_date' => '2026-10-20',
            'schedule' => 'Thứ 3 - 5 - 7 (18:30 - 20:30)',
            'tuition_fee' => 2800000,
            'max_students' => 25,
            'status' => 'ongoing',
        ]);
        $class3 = ClassRoom::create([
            'code' => 'TAGT-2601',
            'name' => 'Giao Tiếp Công Sở K01 - Cuối tuần',
            'course_id' => $courseComm->id,
            'teacher_id' => $gv2->id,
            'start_date' => '2026-11-01',
            'schedule' => 'Thứ 7 - CN (08:30 - 10:30)',
            'tuition_fee' => 2200000,
            'max_students' => 15,
            'status' => 'upcoming',
        ]);

        // 6. Tạo Đăng ký lớp & Điểm mẫu
        Enrollment::create([
            'class_room_id' => $class1->id,
            'student_id' => $students[0]->id,
            'enrollment_date' => '2026-10-10',
            'paid_tuition' => 4500000,
            'payment_status' => 'paid',
            'midterm_score' => 7.0,
            'final_score' => 7.5,
            'result_status' => 'passed',
            'remarks' => 'Nắm vững cấu trúc Writing Task 2.',
        ]);
        Enrollment::create([
            'class_room_id' => $class1->id,
            'student_id' => $students[1]->id,
            'enrollment_date' => '2026-10-11',
            'paid_tuition' => 4500000,
            'payment_status' => 'paid',
            'midterm_score' => 6.5,
            'final_score' => null,
            'result_status' => 'studying',
        ]);
        Enrollment::create([
            'class_room_id' => $class2->id,
            'student_id' => $students[0]->id,
            'enrollment_date' => '2026-10-12',
            'paid_tuition' => 2800000,
            'payment_status' => 'paid',
        ]);

        // 7. Tạo Điểm danh mẫu
        Attendance::create([
            'class_room_id' => $class1->id,
            'student_id' => $students[0]->id,
            'attendance_date' => '2026-10-15',
            'status' => 'present',
            'note' => 'Đi học đúng giờ',
        ]);
        Attendance::create([
            'class_room_id' => $class1->id,
            'student_id' => $students[1]->id,
            'attendance_date' => '2026-10-15',
            'status' => 'late',
            'note' => 'Kẹt xe trễ 10 phút',
        ]);
    }
}

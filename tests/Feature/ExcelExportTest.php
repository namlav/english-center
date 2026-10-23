<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teacherUser;
    private Teacher $teacher;
    private User $studentUser;
    private Student $student;
    private ClassRoom $classRoom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->teacherUser = User::factory()->create(['role' => 'teacher']);
        $this->teacher = Teacher::create([
            'user_id' => $this->teacherUser->id,
            'teacher_code' => 'GV001',
            'specialization' => 'IELTS',
        ]);

        $this->studentUser = User::factory()->create(['name' => 'Nguyễn Văn A', 'role' => 'student']);
        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'student_code' => 'HV0001',
        ]);

        $course = Course::create([
            'code' => 'IELTS-ADV',
            'name' => 'IELTS Advanced 7.5+',
            'level' => 'Advanced',
            'duration_hours' => 60,
            'status' => 'active',
        ]);

        $this->classRoom = ClassRoom::create([
            'course_id' => $course->id,
            'teacher_id' => $this->teacher->id,
            'code' => 'IELTS-A01',
            'name' => 'Lớp IELTS Sáng 2-4-6',
            'start_date' => now()->toDateString(),
            'schedule' => 'Thứ 2-4-6 (08:00 - 10:00)',
            'tuition_fee' => 5000000,
            'max_students' => 20,
            'status' => 'ongoing',
        ]);

        Enrollment::create([
            'student_id' => $this->student->id,
            'class_room_id' => $this->classRoom->id,
            'enrollment_date' => now()->toDateString(),
            'paid_tuition' => 5000000,
            'payment_status' => 'paid',
            'midterm_score' => 8.5,
            'final_score' => 9.0,
            'result_status' => 'passed',
            'remarks' => 'Học tập xuất sắc',
        ]);
    }

    public function test_admin_and_teacher_can_export_grades_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('grades.export', $this->classRoom));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('HV0001', $content);
        $this->assertStringContainsString('Nguyễn Văn A', $content);
        $this->assertStringContainsString('8.5', $content);
        $this->assertStringContainsString('9', $content);
        $this->assertStringContainsString('Đạt', $content);

        // Teacher of class can also export
        $teacherResponse = $this->actingAs($this->teacherUser)->get(route('grades.export', $this->classRoom));
        $teacherResponse->assertOk();
    }

    public function test_admin_and_teacher_can_view_printable_grade_sheet(): void
    {
        $response = $this->actingAs($this->admin)->get(route('grades.print', $this->classRoom));

        $response->assertOk();
        $response->assertSee('BẢNG ĐIỂM HỌC VIÊN');
        $response->assertSee('IELTS-A01');
        $response->assertSee('Nguyễn Văn A');
        $response->assertSee('HV0001');
        $response->assertSee('window.print()', false);
    }

    public function test_admin_and_teacher_can_export_attendances_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('attendances.export', [
            'classRoom' => $this->classRoom,
            'attendance_date' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Mã Học Viên', $content);
        $this->assertStringContainsString('HV0001', $content);
        $this->assertStringContainsString('Nguyễn Văn A', $content);
    }

    public function test_admin_can_export_revenue_report_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('IELTS Advanced 7.5+', $content);
        $this->assertStringContainsString('IELTS-A01', $content);
        $this->assertStringContainsString('5.000.000', $content);
        $this->assertStringContainsString('TỔNG CỘNG', $content);
    }

    public function test_student_and_unauthorized_teacher_are_forbidden(): void
    {
        // Student forbidden from export
        $this->actingAs($this->studentUser)->get(route('grades.export', $this->classRoom))->assertForbidden();
        $this->actingAs($this->studentUser)->get(route('attendances.export', $this->classRoom))->assertForbidden();
        $this->actingAs($this->studentUser)->get(route('reports.export'))->assertForbidden();

        // Other teacher forbidden from reports and other classes
        $otherTeacherUser = User::factory()->create(['role' => 'teacher']);
        Teacher::create(['user_id' => $otherTeacherUser->id, 'teacher_code' => 'GV002', 'specialization' => 'TOEIC']);

        $this->actingAs($otherTeacherUser)->get(route('reports.export'))->assertForbidden();
        $this->actingAs($otherTeacherUser)->get(route('grades.export', $this->classRoom))->assertForbidden();
    }
}

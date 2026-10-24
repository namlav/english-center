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

class ClassRoomRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createClassRoom(array $attrs = []): ClassRoom
    {
        $teacherUser = User::factory()->create(['name' => 'Giảng viên Demo', 'role' => 'teacher']);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'teacher_code' => 'GV099',
            'specialization' => 'IELTS',
        ]);
        $course = Course::create([
            'code' => 'CRS-TEST',
            'name' => 'Khóa học Test',
            'slug' => 'khoa-hoc-test',
            'level' => 'intermediate',
            'price' => 2000000,
            'is_published_online' => true,
        ]);

        return ClassRoom::create(array_merge([
            'name' => 'Lớp Test Khai Giảng',
            'code' => 'TEST-001',
            'course_id' => $course->id,
            'teacher_id' => $teacher->id,
            'schedule' => 'Thứ 2, 4, 6 18:00-19:30',
            'start_date' => now()->addDays(7)->toDateString(),
            'expected_end_date' => now()->addMonths(2)->toDateString(),
            'max_students' => 20,
            'tuition_fee' => 2500000,
            'status' => 'upcoming',
        ], $attrs));
    }

    public function test_can_view_class_registration_form(): void
    {
        $classRoom = $this->createClassRoom();

        $response = $this->get(route('public_classes.register', $classRoom));

        $response->assertOk();
        $response->assertSee('Thông tin học viên');
        $response->assertSee($classRoom->name);
        $response->assertSee(number_format($classRoom->tuition_fee, 0, ',', '.') . ' ₫');
        $response->assertSee('Chuyển khoản học phí');
        $response->assertSee('Thanh toán');
    }

    public function test_guest_can_register_class_and_receives_unpaid_enrollment(): void
    {
        $classRoom = $this->createClassRoom();

        $response = $this->post(route('public_classes.submit_register', $classRoom), [
            'name' => 'Nguyễn Minh Khang',
            'phone' => '0988776655',
            'email' => 'khang.demo@englishcenter.vn',
            'date_of_birth' => '2001-08-20',
            'gender' => 'male',
            'address' => 'Hà Nội',
            'learning_goals' => 'Đạt TOEIC 700+',
            'notes' => 'Tư vấn xếp lớp ca tối',
            'payment_method' => 'bank_transfer',
        ]);

        $this->assertAuthenticated();

        $user = User::where('email', 'khang.demo@englishcenter.vn')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Nguyễn Minh Khang', $user->name);
        $this->assertEquals('0988776655', $user->phone);

        $student = $user->student;
        $this->assertNotNull($student);
        $this->assertEquals('male', $student->gender);
        $this->assertEquals('Hà Nội', $student->address);

        $enrollment = Enrollment::where('class_room_id', $classRoom->id)
            ->where('student_id', $student->id)
            ->first();

        $this->assertNotNull($enrollment);
        $this->assertEquals('unpaid', $enrollment->payment_status);
        $this->assertEquals(0, (float) $enrollment->paid_tuition);
        $this->assertStringContainsString('Chuyển khoản QR', $enrollment->remarks);

        $response->assertRedirect(route('public_classes.confirmation', [
            'classRoom' => $classRoom->id,
            'enrollment' => $enrollment->id,
        ]));
    }

    public function test_admin_can_mark_invoice_as_paid(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = $this->createClassRoom(['tuition_fee' => 3000000]);
        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::create(['user_id' => $studentUser->id, 'student_code' => 'HV999']);

        $enrollment = Enrollment::create([
            'class_room_id' => $classRoom->id,
            'student_id' => $student->id,
            'enrollment_date' => now()->toDateString(),
            'paid_tuition' => 0,
            'payment_status' => 'unpaid',
            'result_status' => 'studying',
        ]);

        $response = $this->actingAs($admin)->post(route('invoices.mark_paid', $enrollment));

        $response->assertSessionHas('success');

        $enrollment->refresh();
        $this->assertEquals('paid', $enrollment->payment_status);
        $this->assertEquals(3000000, (float) $enrollment->paid_tuition);
    }

    public function test_student_can_view_my_classes_page(): void
    {
        $classRoom = $this->createClassRoom();
        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::create(['user_id' => $studentUser->id, 'student_code' => 'HV998']);

        Enrollment::create([
            'class_room_id' => $classRoom->id,
            'student_id' => $student->id,
            'enrollment_date' => now()->toDateString(),
            'paid_tuition' => 0,
            'payment_status' => 'unpaid',
            'result_status' => 'studying',
        ]);

        $response = $this->actingAs($studentUser)->get(route('enrollments.my_classes'));

        $response->assertOk();
        $response->assertSee('Lớp học trực tiếp của tôi');
        $response->assertSee($classRoom->name);
        $response->assertSee('Chờ Admin duyệt học phí');
    }
}

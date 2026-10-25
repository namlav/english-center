<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminClassEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_enroll_student_into_classroom(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create([
            'max_students' => 10,
            'tuition_fee' => 2000000,
        ]);
        $student = Student::factory()->create();

        $response = $this->actingAs($admin)->post(route('enrollments.admin_enroll', $classRoom), [
            'student_id' => $student->id,
            'paid_tuition' => 2000000,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('enrollments', [
            'class_room_id' => $classRoom->id,
            'student_id' => $student->id,
            'payment_status' => 'paid',
            'result_status' => 'studying',
        ]);
    }

    public function test_cannot_enroll_student_twice_into_same_classroom(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create(['max_students' => 10]);
        $student = Student::factory()->create();

        Enrollment::create([
            'class_room_id' => $classRoom->id,
            'student_id' => $student->id,
            'enrollment_date' => now()->toDateString(),
            'paid_tuition' => 1000000,
            'payment_status' => 'paid',
            'result_status' => 'studying',
        ]);

        $response = $this->actingAs($admin)->post(route('enrollments.admin_enroll', $classRoom), [
            'student_id' => $student->id,
        ]);

        $response->assertSessionHasErrors('student_id');
        $this->assertEquals(1, Enrollment::where('class_room_id', $classRoom->id)->count());
    }

    public function test_cannot_enroll_when_classroom_is_full(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create(['max_students' => 1]);
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        Enrollment::create([
            'class_room_id' => $classRoom->id,
            'student_id' => $student1->id,
            'enrollment_date' => now()->toDateString(),
            'paid_tuition' => 1000000,
            'payment_status' => 'paid',
            'result_status' => 'studying',
        ]);

        $response = $this->actingAs($admin)->post(route('enrollments.admin_enroll', $classRoom), [
            'student_id' => $student2->id,
        ]);

        $response->assertSessionHasErrors('student_id');
        $this->assertEquals(1, Enrollment::where('class_room_id', $classRoom->id)->count());
    }

    public function test_admin_can_remove_student_from_classroom(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create();
        $student = Student::factory()->create();

        $enrollment = Enrollment::create([
            'class_room_id' => $classRoom->id,
            'student_id' => $student->id,
            'enrollment_date' => now()->toDateString(),
            'paid_tuition' => 1000000,
            'payment_status' => 'paid',
            'result_status' => 'studying',
        ]);

        $response = $this->actingAs($admin)->delete(route('enrollments.admin_remove', [$classRoom, $enrollment]));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }

    public function test_non_admin_cannot_admin_enroll(): void
    {
        $studentUser = User::factory()->student()->create();
        $classRoom = ClassRoom::factory()->create();
        $student = Student::factory()->create();

        $response = $this->actingAs($studentUser)->post(route('enrollments.admin_enroll', $classRoom), [
            'student_id' => $student->id,
        ]);

        $response->assertForbidden();
    }
}

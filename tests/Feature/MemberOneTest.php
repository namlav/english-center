<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MemberOneTest extends TestCase
{
    use RefreshDatabase;

    public static function adminPages(): array
    {
        return [['courses.index'], ['courses.create'], ['courses.show'], ['courses.edit'], ['class_rooms.index'], ['class_rooms.create'], ['class_rooms.show'], ['class_rooms.edit']];
    }

    #[DataProvider('adminPages')]
    public function test_admin_can_open_all_course_and_classroom_pages(string $route): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create();
        $record = str_starts_with($route, 'courses.') ? $classRoom->course : $classRoom;
        $parameters = str_ends_with($route, '.show') || str_ends_with($route, '.edit') ? [$record] : [];

        $this->actingAs($admin)->get(route($route, $parameters))->assertOk();
    }

    public static function forbiddenRoles(): array
    {
        return [['student'], ['teacher']];
    }

    #[DataProvider('forbiddenRoles')]
    public function test_non_admin_cannot_create_course_or_classroom(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user)->post(route('courses.store'), [])->assertForbidden();
        $this->post(route('class_rooms.store'), [])->assertForbidden();
        $this->assertDatabaseCount('courses', 0);
        $this->assertDatabaseCount('class_rooms', 0);
    }

    #[DataProvider('forbiddenRoles')]
    public function test_non_admin_cannot_edit_or_delete_classroom(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $classRoom = ClassRoom::factory()->create();
        $this->actingAs($user)->put(route('class_rooms.update', $classRoom), $this->classPayload($classRoom))->assertForbidden();
        $this->delete(route('class_rooms.destroy', $classRoom))->assertForbidden();
        $this->assertModelExists($classRoom);
    }

    #[DataProvider('forbiddenRoles')]
    public function test_authenticated_teacher_and_student_can_read_class_detail(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $classRoom = ClassRoom::factory()->create();

        $this->actingAs($user)->get(route('class_rooms.show', $classRoom))->assertOk()->assertSee($classRoom->code);
    }

    public function test_create_path_opens_form_instead_of_being_bound_as_class_id(): void
    {
        $admin = User::factory()->admin()->create();
        Course::factory()->create();
        Teacher::factory()->create();

        $this->actingAs($admin)->get('/class-rooms/create')->assertOk()->assertViewIs('class_rooms.form');
    }

    public function test_admin_creates_course_and_updates_its_status(): void
    {
        $admin = User::factory()->admin()->create();
        $payload = ['code' => 'KH-NEW', 'name' => 'Khóa mới', 'duration_hours' => 60, 'level' => 'A1', 'is_active' => 1];
        $this->actingAs($admin)->post(route('courses.store'), $payload)->assertSessionHas('success');
        $course = Course::where('code', 'KH-NEW')->sole();

        $this->put(route('courses.update', $course), [...$payload, 'is_active' => 0])->assertRedirectToRoute('courses.show', $course);

        $this->assertDatabaseHas('courses', ['id' => $course->id, 'is_active' => false, 'duration_hours' => 60]);
    }

    public function test_duplicate_course_code_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Course::factory()->create(['code' => 'EXISTS']);

        $this->actingAs($admin)->post(route('courses.store'), ['code' => 'EXISTS', 'name' => 'Trùng mã', 'duration_hours' => 48, 'is_active' => 1])
            ->assertSessionHasErrors(['code' => 'Mã khóa học đã tồn tại.']);

        $this->assertDatabaseCount('courses', 1);
    }

    public function test_course_with_classroom_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create();

        $this->actingAs($admin)->delete(route('courses.destroy', $classRoom->course))->assertSessionHas('error');

        $this->assertModelExists($classRoom->course);
        $this->assertModelExists($classRoom);
    }

    public function test_unused_course_can_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->actingAs($admin)->delete(route('courses.destroy', $course))->assertRedirectToRoute('courses.index')->assertSessionHas('success');

        $this->assertModelMissing($course);
    }

    public function test_admin_creates_class_with_tuition_on_classroom_and_ignores_unexpected_id(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->make(['course_id' => Course::factory()->create()->id, 'teacher_id' => Teacher::factory()->create()->id]);

        $this->actingAs($admin)->post(route('class_rooms.store'), [...$this->classPayload($classRoom), 'id' => 9999])->assertSessionHas('success');

        $created = ClassRoom::where('code', $classRoom->code)->sole();
        $this->assertSame('2500000.00', $created->tuition_fee);
        $this->assertSame(25, $created->max_students);
        $this->assertNotSame(9999, $created->id);
    }

    public function test_filter_combines_keyword_course_teacher_and_status(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create();
        $wanted = ClassRoom::factory()->for($teacher)->for($course)->create(['name' => 'IELTS đúng', 'code' => 'WANTED', 'status' => 'ongoing']);
        ClassRoom::factory()->for($teacher)->for($course)->create(['name' => 'IELTS sai trạng thái', 'code' => 'WRONG-STATUS', 'status' => 'completed']);
        ClassRoom::factory()->for($course)->create(['name' => 'IELTS sai giáo viên', 'code' => 'WRONG-TEACHER', 'status' => 'ongoing']);
        ClassRoom::factory()->for($teacher)->create(['name' => 'IELTS sai khóa', 'code' => 'WRONG-COURSE', 'status' => 'ongoing']);

        $this->actingAs($admin)->get(route('class_rooms.index', ['keyword' => 'IELTS', 'course_id' => $course->id, 'teacher_id' => $teacher->id, 'status' => 'ongoing']))
            ->assertOk()->assertSee($wanted->code)->assertDontSee('WRONG-STATUS')->assertDontSee('WRONG-TEACHER')->assertDontSee('WRONG-COURSE');
    }

    public function test_inactive_course_and_teacher_cannot_be_newly_assigned(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['is_active' => false]);
        $teacher = Teacher::factory()->create();
        $teacher->user->update(['is_active' => false]);
        $classRoom = ClassRoom::factory()->make(['course_id' => $course->id, 'teacher_id' => $teacher->id]);

        $this->actingAs($admin)->post(route('class_rooms.store'), $this->classPayload($classRoom))->assertSessionHasErrors(['course_id', 'teacher_id']);

        $this->assertDatabaseCount('class_rooms', 0);
    }

    public function test_current_inactive_course_and_teacher_can_be_retained_on_edit(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create();
        $classRoom->course->update(['is_active' => false]);
        $classRoom->teacher->user->update(['is_active' => false]);

        $this->actingAs($admin)->put(route('class_rooms.update', $classRoom), [...$this->classPayload($classRoom), 'name' => 'Tên đã sửa'])->assertSessionHas('success');

        $this->assertDatabaseHas('class_rooms', ['id' => $classRoom->id, 'name' => 'Tên đã sửa']);
    }

    public function test_capacity_cannot_be_reduced_below_enrollment_count(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create();
        Enrollment::factory()->count(2)->create(['class_room_id' => $classRoom->id]);

        $this->actingAs($admin)->put(route('class_rooms.update', $classRoom), [...$this->classPayload($classRoom), 'max_students' => 1])->assertSessionHasErrors('max_students');

        $this->assertSame(25, $classRoom->fresh()->max_students);
    }

    public function test_course_and_tuition_are_locked_after_registration(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();
        $other = Course::factory()->create();

        $this->actingAs($admin)->put(route('class_rooms.update', $enrollment->classRoom), [...$this->classPayload($enrollment->classRoom), 'course_id' => $other->id, 'tuition_fee' => '999.00'])->assertSessionHasErrors('course_id');

        $this->assertSame('2500000.00', $enrollment->classRoom->fresh()->tuition_fee);
    }

    public function test_unchanged_fee_in_integer_format_is_allowed_after_registration(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($admin)->put(route('class_rooms.update', $enrollment->classRoom), [...$this->classPayload($enrollment->classRoom), 'tuition_fee' => '2500000'])->assertSessionHas('success');
    }

    public function test_enrolled_class_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $enrollment = Enrollment::factory()->create();

        $this->actingAs($admin)->delete(route('class_rooms.destroy', $enrollment->classRoom))->assertSessionHas('error');

        $this->assertModelExists($enrollment);
        $this->assertModelExists($enrollment->classRoom);
    }

    public function test_empty_class_can_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create();

        $this->actingAs($admin)->delete(route('class_rooms.destroy', $classRoom))->assertSessionHas('success');

        $this->assertModelMissing($classRoom);
    }

    public static function invalidClassValues(): array
    {
        return [['max_students', 0], ['tuition_fee', -1], ['tuition_fee', '10.001'], ['schedule', ''], ['status', 'unknown'], ['start_date', '2026-02-30']];
    }

    #[DataProvider('invalidClassValues')]
    public function test_classroom_rejects_invalid_input(string $field, string|int $value): void
    {
        $admin = User::factory()->admin()->create();
        $classRoom = ClassRoom::factory()->create();

        $this->actingAs($admin)->put(route('class_rooms.update', $classRoom), [...$this->classPayload($classRoom), $field => $value])->assertSessionHasErrors($field);
    }

    public function test_database_prevents_attendance_for_student_outside_class_roster(): void
    {
        $classRoom = ClassRoom::factory()->create();
        $student = Student::factory()->create();

        $this->expectException(QueryException::class);

        Attendance::create(['class_room_id' => $classRoom->id, 'student_id' => $student->id, 'attendance_date' => '2026-10-01', 'status' => 'present']);
    }

    public function test_database_prevents_cascading_delete_of_enrolled_classroom(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->expectException(QueryException::class);

        $enrollment->classRoom->delete();
    }

    public function test_database_rejects_grade_above_ten(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->expectException(QueryException::class);

        $enrollment->update(['final_score' => 11]);
    }

    public function test_search_values_and_class_names_are_escaped(): void
    {
        $admin = User::factory()->admin()->create();
        ClassRoom::factory()->create(['name' => '<script>alert("class")</script>', 'schedule' => '<img src=x onerror=alert(1)>']);

        $this->actingAs($admin)->get(route('class_rooms.index'))->assertOk()->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("class")</script>', false)->assertSee('&lt;img', false)->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    public function test_database_seeder_is_repeatable_and_preserves_existing_changes(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 16)->setTime(12, 0));
        $this->seed(DatabaseSeeder::class);
        $course = Course::where('code', 'KH-IELTS')->sole();
        $course->update(['name' => 'Nội dung đã sửa']);
        $user = User::where('email', 'admin@englishcenter.vn')->sole();
        $user->update(['name' => 'Admin đã sửa']);
        $tables = ['users', 'teachers', 'students', 'courses', 'class_rooms', 'enrollments', 'attendances'];
        $before = [];
        foreach ($tables as $table) {
            $before[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }

        $this->seed(DatabaseSeeder::class);

        foreach ($tables as $table) {
            $this->assertSame($before[$table], DB::table($table)->orderBy('id')->get()->toJson(), $table.' changed on second seed');
        }
        $this->assertDatabaseCount('students', 24);
        $this->assertDatabaseCount('teachers', 3);
        $this->assertDatabaseCount('enrollments', 40);
        $this->assertDatabaseCount('attendances', 72);
        $this->artisan('data:audit', ['--json' => true])->assertExitCode(0);
    }

    public function test_existing_member_three_relations_still_resolve(): void
    {
        $attendance = Attendance::factory()->create();

        $this->assertSame($attendance->student_id, $attendance->classRoom->students()->sole()->id);
        $this->assertSame($attendance->class_room_id, $attendance->student->classRooms()->sole()->id);
        $this->assertSame($attendance->class_room_id, $attendance->student->enrollments()->sole()->classRoom->id);
    }

    public function test_seeded_admin_can_sign_in_with_existing_demo_password(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post(route('login.post'), ['email' => 'admin@englishcenter.vn', 'password' => '123456'])
            ->assertRedirectToRoute('class_rooms.index');

        $this->assertAuthenticatedAs(User::where('email', 'admin@englishcenter.vn')->sole());
    }

    private function classPayload(ClassRoom $classRoom): array
    {
        return [
            'code' => $classRoom->code, 'name' => $classRoom->name, 'course_id' => $classRoom->course_id, 'teacher_id' => $classRoom->teacher_id,
            'start_date' => $classRoom->start_date->format('Y-m-d'), 'schedule' => $classRoom->schedule,
            'tuition_fee' => $classRoom->tuition_fee, 'max_students' => $classRoom->max_students, 'status' => $classRoom->status,
        ];
    }
}

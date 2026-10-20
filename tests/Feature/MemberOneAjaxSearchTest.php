<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MemberOneAjaxSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_live_search_combines_keyword_and_inactive_status(): void
    {
        $admin = User::factory()->admin()->create();
        Course::factory()->create(['name' => 'IELTS tạm đóng', 'code' => 'KH-MATCH', 'is_active' => false]);
        Course::factory()->create(['name' => 'IELTS đang mở', 'code' => 'KH-ACTIVE', 'is_active' => true]);
        Course::factory()->create(['name' => 'TOEIC tạm đóng', 'code' => 'KH-OTHER', 'is_active' => false]);

        $response = $this->actingAs($admin)->getJson(route('courses.index', ['keyword' => ' IELTS ', 'status' => '0']));

        $response->assertOk()->assertJsonPath('total', 1);
        $html = $response->json('html');
        $this->assertStringContainsString('KH-MATCH', $html);
        $this->assertStringNotContainsString('KH-ACTIVE', $html);
        $this->assertStringNotContainsString('KH-OTHER', $html);
        $this->assertStringNotContainsString('<html', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function test_classroom_live_search_combines_all_filters(): void
    {
        $user = User::factory()->student()->create();
        $teacher = Teacher::factory()->create();
        $course = Course::factory()->create();
        ClassRoom::factory()->for($teacher)->for($course)->create(['name' => 'IELTS đúng', 'code' => 'CLASS-MATCH', 'status' => 'ongoing']);
        ClassRoom::factory()->for($teacher)->for($course)->create(['name' => 'IELTS khác trạng thái', 'code' => 'CLASS-STATUS', 'status' => 'completed']);
        ClassRoom::factory()->for($course)->create(['name' => 'IELTS khác giáo viên', 'code' => 'CLASS-TEACHER', 'status' => 'ongoing']);
        ClassRoom::factory()->for($teacher)->create(['name' => 'IELTS khác khóa học', 'code' => 'CLASS-COURSE', 'status' => 'ongoing']);

        $response = $this->actingAs($user)->getJson(route('class_rooms.index', [
            'keyword' => 'IELTS', 'teacher_id' => $teacher->id, 'course_id' => $course->id, 'status' => 'ongoing',
        ]));

        $response->assertOk()->assertJsonPath('total', 1);
        $html = $response->json('html');
        $this->assertStringContainsString('CLASS-MATCH', $html);
        $this->assertStringNotContainsString('CLASS-STATUS', $html);
        $this->assertStringNotContainsString('CLASS-TEACHER', $html);
        $this->assertStringNotContainsString('CLASS-COURSE', $html);
        $this->assertStringNotContainsString('/edit', $html);
        $this->assertStringNotContainsString('name="_method"', $html);
    }

    public function test_course_live_search_returns_correct_page_and_preserves_filter_links(): void
    {
        $admin = User::factory()->admin()->create();
        foreach (range(1, 12) as $i) {
            Course::factory()->create(['code' => sprintf('AJAX-%02d', $i), 'is_active' => true]);
        }

        $response = $this->actingAs($admin)->getJson(route('courses.index', ['keyword' => 'AJAX-', 'status' => '1', 'page' => 2]));

        $response->assertOk()->assertJsonPath('total', 12);
        $html = $response->json('html');
        $this->assertStringContainsString('AJAX-01', $html);
        $this->assertStringContainsString('AJAX-02', $html);
        $this->assertStringNotContainsString('AJAX-03', $html);
        $this->assertStringContainsString('keyword=AJAX-', $html);
        $this->assertStringContainsString('status=1', $html);
        $this->assertStringContainsString('page=1', $html);
    }

    public static function pages(): array
    {
        return [['courses.index'], ['class_rooms.index']];
    }

    #[DataProvider('pages')]
    public function test_no_match_returns_empty_state_without_a_full_page(string $route): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->getJson(route($route, ['keyword' => 'no-match']));

        $response->assertOk()->assertJsonPath('total', 0);
        $this->assertStringContainsString('colspan=', $response->json('html'));
        $this->assertStringNotContainsString('<html', $response->json('html'));
    }

    #[DataProvider('pages')]
    public function test_plain_get_still_renders_working_search_form_and_results(string $route): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route($route))->assertOk()
            ->assertSee('data-search-form', false)->assertSee('data-search-results', false)
            ->assertSee('data-search-reset', false)->assertSee('data-search-retry', false)
            ->assertSee('member-one-search.js')->assertSee('<html', false);
    }

    public static function invalidFilters(): array
    {
        return [
            ['courses.index', 'keyword', str_repeat('a', 101)],
            ['courses.index', 'status', 'invalid'],
            ['courses.index', 'page', 0],
            ['class_rooms.index', 'keyword', str_repeat('a', 101)],
            ['class_rooms.index', 'course_id', -1],
            ['class_rooms.index', 'teacher_id', -1],
            ['class_rooms.index', 'status', 'invalid'],
            ['class_rooms.index', 'page', 'bad-page'],
        ];
    }

    #[DataProvider('invalidFilters')]
    public function test_invalid_filters_return_json_validation_errors(string $route, string $field, string|int $value): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->getJson(route($route, [$field => $value]))
            ->assertUnprocessable()->assertJsonValidationErrors($field);
    }

    #[DataProvider('pages')]
    public function test_expired_or_missing_session_cannot_fetch_results(string $route): void
    {
        $this->getJson(route($route))->assertUnauthorized();
    }

    public static function nonAdminRoles(): array
    {
        return [['student'], ['teacher']];
    }

    #[DataProvider('nonAdminRoles')]
    public function test_course_ajax_endpoint_keeps_admin_only_access(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->getJson(route('courses.index'))->assertForbidden();
    }

    public function test_ajax_partial_escapes_class_name_and_keeps_teacher_actions_scoped(): void
    {
        $teacher = Teacher::factory()->create();
        $mine = ClassRoom::factory()->for($teacher)->create(['name' => '<script>alert("class")</script>']);
        $other = ClassRoom::factory()->create();

        $response = $this->actingAs($teacher->user)->getJson(route('class_rooms.index'));

        $response->assertOk()->assertJsonPath('total', 2);
        $html = $response->json('html');
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert("class")</script>', $html);
        $this->assertStringContainsString(route('grades.index', $mine), $html);
        $this->assertStringNotContainsString(route('grades.index', $other), $html);
        $this->assertStringNotContainsString('/edit', $html);
    }

    public function test_xhr_request_returns_json_for_the_live_search_client(): void
    {
        $admin = User::factory()->admin()->create();
        Course::factory()->create(['code' => 'XHR-COURSE']);

        $response = $this->actingAs($admin)->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json',
        ])->get(route('courses.index', ['keyword' => 'XHR-COURSE']));

        $response->assertOk()->assertHeader('Content-Type', 'application/json')->assertJsonPath('total', 1);
        $this->assertStringContainsString('XHR-COURSE', $response->json('html'));
    }
}

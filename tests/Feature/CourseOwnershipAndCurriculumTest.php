<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Database\Seeders\WebsiteContentSeeder;
use Database\Seeders\CourseShopSeeder;
use Database\Seeders\CoursePresentationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseOwnershipAndCurriculumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\SiteSetting::where('key', 'payment_bank')->update(['value' => ['enabled' => false]]);
        $this->seed([
            WebsiteContentSeeder::class,
            CourseShopSeeder::class,
            CoursePresentationSeeder::class,
        ]);
    }

    public function test_all_courses_have_published_lessons_and_are_for_sale(): void
    {
        $courses = Course::all();
        $this->assertGreaterThan(20, $courses->count());

        foreach ($courses as $course) {
            $this->assertTrue((bool)$course->is_for_sale, "Course {$course->slug} should be for sale");
            $this->assertGreaterThan(0, $course->lessons()->where('is_published', true)->count(), "Course {$course->slug} should have published lessons");
        }
    }

    public function test_shop_catalog_does_not_show_coming_soon(): void
    {
        $response = $this->get(route('catalog.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Chương trình sắp mở');
    }

    public function test_owned_course_displays_da_mua_badge_and_button(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::first();

        // Before purchasing: shows "Thêm vào giỏ hàng"
        $response = $this->actingAs($user)->get(route('catalog.index'));
        $response->assertStatus(200);
        $response->assertSee('Thêm vào giỏ hàng');

        // Enroll user in course (active enrollment)
        CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // After owning: shop card shows "Đã mua"
        $shopResponse = $this->actingAs($user)->get(route('catalog.index'));
        $shopResponse->assertStatus(200);
        $shopResponse->assertSee('Đã mua');

        // Course detail page also displays "Đã mua" and "Khóa học đã mua"
        $detailResponse = $this->actingAs($user)->get(route('shop.show', $course->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Khóa học đã mua');
        $detailResponse->assertSee('Đã mua');
    }

    public function test_guest_can_study_trial_lesson_for_free_and_see_study4_curriculum(): void
    {
        $course = Course::where('catalog_key', 'course-15')->first();
        if (!$course) {
            $course = Course::first();
        }

        $detailResponse = $this->get(route('shop.show', $course->slug));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Chương trình học');
        $detailResponse->assertSee('Học thử miễn phí');
        $detailResponse->assertSee('Học thử');

        // Find a trial lesson (preview = true)
        $trialLesson = $course->lessons()->where('is_preview', true)->first();
        $this->assertNotNull($trialLesson);

        // Guest accesses trial lesson without login
        $lessonResponse = $this->get(route('learn.lesson', [$course->slug, $trialLesson->id]));
        $lessonResponse->assertStatus(200);
        $lessonResponse->assertSee($trialLesson->title);
        $lessonResponse->assertSee('HỌC THỬ MIỄN PHÍ');

        // Non-trial lesson should be forbidden without purchase
        $lockedLesson = $course->lessons()->where('is_preview', false)->first();
        if ($lockedLesson) {
            $lockedResponse = $this->get(route('learn.lesson', [$course->slug, $lockedLesson->id]));
            $lockedResponse->assertStatus(403);
        }
    }
}

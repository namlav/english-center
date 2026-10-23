<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\User;
use App\Services\CourseShop;
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

    public function test_combo_purchase_grants_access_to_combo_and_included_courses(): void
    {
        $user = User::factory()->student()->create();
        $admin = User::factory()->admin()->create();
        $shop = app(CourseShop::class);

        $combo = Course::where('catalog_key', 'series-2')->first();
        $childCourse = Course::where('catalog_key', 'course-15')->first();

        if ($combo && $childCourse) {
            // Create and confirm order for the combo
            $order = Order::create([
                'number' => (string) \Illuminate\Support\Str::uuid(),
                'checkout_token' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'buyer_name' => $user->name,
                'buyer_email' => $user->email,
                'total' => 1650000,
                'status' => 'pending',
            ]);
            $order->items()->create([
                'course_id' => $combo->id,
                'course_name' => $combo->name,
                'unit_price' => 1650000,
                'access_days' => 365,
            ]);
            $payment = $order->payment()->create([
                'reference' => 'DEMO-' . strtoupper(\Illuminate\Support\Str::random(20)),
                'amount' => 1650000,
                'method' => 'demo',
                'status' => 'pending',
            ]);

            $shop->confirm($order, $payment->reference, $admin);

            // User now owns both combo and child course
            $this->assertTrue($shop->owned($user, $combo->id));
            $this->assertTrue($shop->owned($user, $childCourse->id));

            // Access child course detail shows "Đã mua"
            $this->actingAs($user)->get(route('shop.show', $childCourse->slug))
                ->assertOk()
                ->assertSee('Đã mua');
        }
    }

    public function test_cart_auto_removes_already_owned_courses(): void
    {
        $user = User::factory()->student()->create();
        $course = Course::first();
        $shop = app(CourseShop::class);

        // Grant enrollment
        CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'source' => 'purchase',
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => now()->addDays(365),
        ]);

        // Put into cart_items
        \Illuminate\Support\Facades\DB::table('cart_items')->insert([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // When accessing cart, it auto-removes the owned course
        $response = $this->actingAs($user)->get(route('cart.index'));
        $response->assertOk();
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }
}

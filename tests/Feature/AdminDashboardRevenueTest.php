<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardRevenueTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Boss',
            'email' => 'admin@test.com',
        ]);

        $this->student = User::factory()->create([
            'role' => 'student',
            'name' => 'Học Viên Demo',
            'email' => 'student@test.com',
        ]);
    }

    public function test_admin_can_access_unified_dashboard_with_course_revenue(): void
    {
        $course = Course::create([
            'name' => 'Khóa Học IELTS Siêu Cấp 7.5+',
            'code' => 'IELTS-75',
            'slug' => 'ielts-sieu-cap-75',
            'price' => 599000,
            'is_for_sale' => true,
        ]);

        $order = Order::create([
            'number' => 'ORD-REV-001',
            'checkout_token' => 'tok_rev_001',
            'user_id' => $this->student->id,
            'buyer_name' => 'Nguyễn Văn Mua',
            'buyer_email' => 'nguyenvanmua@gmail.com',
            'total' => 599000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'course_name' => $course->name,
            'unit_price' => 599000,
            'access_days' => 365,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Tổng Quan & Báo Cáo Doanh Thu Khóa Học');
        $response->assertSee('599.000');
        $response->assertSee('Khóa Học IELTS Siêu Cấp 7.5+');
        $response->assertSee('Nguyễn Văn Mua');
    }

    public function test_admin_can_export_course_revenue_csv(): void
    {
        $course = Course::create([
            'name' => 'TOEIC Cấp Tốc 650+',
            'code' => 'TOEIC-650',
            'slug' => 'toeic-cap-toc-650',
            'price' => 299000,
            'is_for_sale' => true,
        ]);

        $order = Order::create([
            'number' => 'ORD-EXP-002',
            'checkout_token' => 'tok_exp_002',
            'user_id' => $this->student->id,
            'buyer_name' => 'Trần Thị Thu',
            'buyer_email' => 'tranthithu@gmail.com',
            'total' => 299000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'course_name' => $course->name,
            'unit_price' => 299000,
            'access_days' => 180,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.revenue.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('ORD-EXP-002', $content);
        $this->assertStringContainsString('Trần Thị Thu', $content);
        $this->assertStringContainsString('TOEIC Cấp Tốc 650+', $content);
        $this->assertStringContainsString('299.000', $content);
        $this->assertStringContainsString('TỔNG DOANH THU THỰC THU', $content);
    }

    public function test_non_admin_cannot_access_admin_dashboard_or_export(): void
    {
        $this->actingAs($this->student)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->student)->get(route('admin.revenue.export'))->assertForbidden();
    }
}

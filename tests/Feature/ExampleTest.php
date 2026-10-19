<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test root redirects to class rooms.
     */
    public function test_root_redirects_to_class_rooms(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('class_rooms.index'));
    }

    /**
     * Test unauthenticated access to class rooms redirects to login.
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/class-rooms');
        $response->assertRedirect(route('login'));
    }

    /**
     * Test login page is accessible.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Đăng nhập hệ thống');
    }

    /**
     * Test authenticated user can access class rooms.
     */
    public function test_authenticated_user_can_access_class_rooms(): void
    {
        $user = User::where('email', 'admin@englishcenter.vn')->first();
        $response = $this->actingAs($user)->get('/class-rooms');
        $response->assertStatus(200);
        $response->assertSee('Danh sách Lớp học');
    }

    /**
     * Test student cannot access admin-only users management.
     */
    public function test_student_cannot_access_users_management(): void
    {
        $studentUser = User::where('role', 'student')->first();
        $response = $this->actingAs($studentUser)->get('/users');
        $response->assertStatus(403);
    }

    /**
     * Test admin can access users management.
     */
    public function test_admin_can_access_users_management(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        $response = $this->actingAs($adminUser)->get('/users');
        $response->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use App\Models\PublicTeacher;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherBannerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_teacher_and_it_automatically_shows_on_banner(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('teachers.store'), [
            'name' => 'Thầy Michael Roberts',
            'email' => 'michael.roberts@englishcenter.vn',
            'password' => '123456',
            'phone' => '0988777666',
            'teacher_code' => 'GV099',
            'specialization' => 'IELTS Speaking 9.0 & Pronunciation',
            'degree' => 'Thạc sĩ Ngôn ngữ Anh Oxford',
            'provider' => 'Đại học Oxford (Anh Quốc)',
            'bio' => 'Chuyên gia bản ngữ với 12 năm kinh nghiệm luyện thi IELTS tại các trung tâm quốc tế.',
            'show_on_banner' => 1,
            'position' => 10,
        ]);

        $response->assertRedirect(route('teachers.index'));
        $response->assertSessionHas('success');

        // Kiểm tra User & Teacher được tạo
        $this->assertDatabaseHas('users', [
            'email' => 'michael.roberts@englishcenter.vn',
            'role' => 'teacher',
        ]);
        $this->assertDatabaseHas('teachers', [
            'teacher_code' => 'GV099',
            'specialization' => 'IELTS Speaking 9.0 & Pronunciation',
        ]);

        // Kiểm tra PublicTeacher được tạo và kích hoạt Banner
        $this->assertDatabaseHas('public_teachers', [
            'name' => 'Thầy Michael Roberts',
            'is_active' => true,
            'position' => 10,
        ]);

        // Kiểm tra hiển thị ngoài trang chủ (Banner Showcase)
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertOk();
        $homeResponse->assertSee('Thầy Michael Roberts');
    }

    public function test_admin_can_update_teacher_and_sync_banner(): void
    {
        $admin = User::factory()->admin()->create();
        $teacherUser = User::factory()->teacher()->create(['name' => 'Cô Minh Anh']);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'teacher_code' => 'GV088',
            'specialization' => 'TOEIC Cấp Tốc',
            'degree' => 'Cử nhân Sư phạm',
        ]);
        PublicTeacher::create([
            'name' => 'Cô Minh Anh',
            'slug' => 'co-minh-anh',
            'bio' => 'Giáo viên TOEIC',
            'photo_path' => 'images/teachers/prep-12.png',
            'provider' => 'English Center',
            'is_active' => true,
            'position' => 5,
        ]);

        $response = $this->actingAs($admin)->put(route('teachers.update', $teacher), [
            'name' => 'Cô Minh Anh - Master TOEIC',
            'email' => $teacherUser->email,
            'phone' => '0912345678',
            'teacher_code' => 'GV088',
            'specialization' => 'TOEIC 950+ Master',
            'degree' => 'Thạc sĩ Sư phạm Anh',
            'provider' => 'Đại học Sư Phạm',
            'bio' => 'Chuyên gia đào tạo TOEIC.',
            'show_on_banner' => 1,
            'position' => 2,
        ]);

        $response->assertRedirect(route('teachers.index'));

        // Kiểm tra PublicTeacher được cập nhật tên và vị trí
        $this->assertDatabaseHas('public_teachers', [
            'name' => 'Cô Minh Anh - Master TOEIC',
            'position' => 2,
            'is_active' => true,
        ]);
    }
}

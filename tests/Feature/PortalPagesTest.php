<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ReferencePage;
use App\Models\PublicTeacher;
use Database\Seeders\WebsiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PortalPagesTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(WebsiteContentSeeder::class); }
    public function test_home_catalog_tests_and_teacher_profiles_are_distinct_public_pages(): void
    {
        $this->get('/')->assertOk()->assertSee('data-teacher-carousel',false)->assertSee('Thầy Jackson Howard')->assertDontSee('id="shop-search"',false)->assertDontSee('id="practice-search"',false);
        $this->get('/courses/online')->assertOk()->assertSee('id="shop-search"',false)->assertDontSee('data-teacher-carousel',false);
        $this->get('/tests')->assertOk()->assertSee('Thư viện đề thi')->assertSee('id="practice-search"',false);
        foreach(PublicTeacher::all() as $teacher){ $this->assertFileExists(public_path($teacher->photo_path)); $this->get(route('public_teachers.show',$teacher->slug))->assertOk()->assertSee($teacher->name)->assertSee('PREP'); }
        $this->get('/?catalog_q=IELTS')->assertRedirect(route('catalog.index',['catalog_q'=>'IELTS']));
        $this->get('/?q=test')->assertRedirect(route('practice.index',['q'=>'test']));
    }
    public function test_account_pages_require_login_and_keep_the_intended_destination(): void
    {
        foreach(['/my-account','/my-account/courses','/my-account/schedule','/admin/website'] as $url)$this->get($url)->assertRedirect(route('login'));
        $user=User::factory()->create(['password'=>'test12345']);
        $this->get('/my-account/courses');
        $this->post(route('login.post'),['email'=>$user->email,'password'=>'test12345'])->assertRedirect(route('account.courses'));
        $this->get('/my-account/courses')->assertOk()->assertSee('Bạn chưa có khóa học nào')->assertSee('id="account-panel"',false);
    }
    public function test_profile_update_only_changes_the_signed_in_users_allowed_fields(): void
    {
        $user=User::factory()->student()->create(); $other=User::factory()->create();
        $this->actingAs($user)->patch('/my-account',['name'=>'Nguyễn An','phone'=>'0901234567','occupation'=>'Sinh viên','role'=>'admin','email'=>$other->email,'user_id'=>$other->id])->assertRedirect(route('account.show'));
        $this->assertSame('student',$user->fresh()->role);$this->assertSame('Nguyễn An',$user->fresh()->name);
        $this->assertSame('Sinh viên',$user->learnerProfile()->sole()->occupation);
        $this->assertNotSame('Nguyễn An',$other->fresh()->name);
        $this->patch('/my-account',['name'=>'','phone'=>'not-phone'])->assertSessionHasErrors(['name','phone']);
    }
    public function test_my_courses_and_schedule_do_not_expose_other_accounts(): void
    {
        $user=User::factory()->student()->create();$other=User::factory()->student()->create();
        foreach([[$user,'Khóa của tôi','Lớp của tôi'],[$other,'Khóa riêng người khác','Lớp riêng người khác']] as [$owner,$courseName,$className]){
            $course=Course::factory()->create(['name'=>$courseName]);
            CourseEnrollment::create(['user_id'=>$owner->id,'course_id'=>$course->id,'source'=>'manual','status'=>'active','started_at'=>now()->subDay()]);
            $student=Student::factory()->create(['user_id'=>$owner->id]); $class=ClassRoom::factory()->create(['name'=>$className,'course_id'=>$course->id]);
            Enrollment::factory()->create(['student_id'=>$student->id,'class_room_id'=>$class->id]);
        }
        $this->actingAs($user)->get('/my-account/courses')->assertOk()->assertSee('Khóa của tôi')->assertDontSee('Khóa riêng người khác');
        $this->get('/my-account/schedule')->assertOk()->assertSee('Lớp của tôi')->assertDontSee('Lớp riêng người khác');
    }
    public function test_website_editing_and_import_are_admin_only(): void
    {
        $student=User::factory()->student()->create(); $page=ReferencePage::first();$teacher=PublicTeacher::first();
        foreach(['/admin/website','/admin/website/home','/admin/website/upgrade-sql','/admin/website/courses/'.$page->id.'/edit','/admin/website/teachers/'.$teacher->id.'/edit'] as $url)$this->actingAs($student)->get($url)->assertForbidden();
        $this->post('/admin/website/import')->assertForbidden();
        $this->patch('/admin/website/courses/'.$page->id,[])->assertForbidden();
        $this->patch('/admin/website/teachers/'.$teacher->id,[])->assertForbidden();
        $this->post('/admin/website/teachers',[])->assertForbidden();
    }
    public function test_catalog_reads_database_and_does_not_recreate_removed_content_on_page_load(): void
    {
        ReferencePage::query()->delete();
        $this->get('/reference-courses')->assertRedirect(route('catalog.index',['q'=>'']));
        $this->get('/khoa-hoc/course-15')->assertNotFound();
        $this->get('/')->assertOk()->assertSee('Khóa học đang được cập nhật');
        $this->assertDatabaseCount('reference_pages',0);
    }
    public function test_import_completes_old_partial_reference_metadata_without_overwriting_edits(): void
    {
        $page=ReferencePage::where('source_key','course-15')->sole();
        $page->update(['title'=>'Tên sửa từ bản cũ','metadata'=>['price'=>600000,'is_visible'=>false]]);
        $this->seed(WebsiteContentSeeder::class);
        $page->refresh();$this->assertSame('Tên sửa từ bản cũ',$page->title);
        $this->assertSame(600000,$page->metadata['price']);$this->assertFalse($page->metadata['is_visible']);
        $this->assertSame('ielts-academic',$page->metadata['catalog_group']);
        $this->get('/khoa-hoc/course-15')->assertNotFound();
    }
    public function test_existing_39_table_installation_has_a_working_upgrade_screen(): void
    {
        Schema::drop('public_teachers');Schema::drop('site_settings');
        $this->get('/')->assertOk()->assertSee('Hồ sơ giáo viên đang được cập nhật');
        $this->actingAs(User::factory()->admin()->create())->get('/admin/website')->assertOk()->assertSee('Bổ sung hai bảng quản lý nội dung');
        $this->get('/admin/website/upgrade-sql')->assertDownload('CAP_NHAT_WEBSITE_41_BANG.sql');
        $this->post('/admin/website/import')->assertRedirect(route('website.index'));
    }
    public function test_admin_edits_course_database_content_and_visibility(): void
    {
        $admin=User::factory()->admin()->create(); $course=ReferencePage::where('source_key','course-15')->sole();
        $data=['title'=>'Listening đã chỉnh sửa','summary'=>'Mô tả đã cập nhật','catalog_group'=>'ielts-academic','price'=>700000,'list_price'=>989000,'position'=>2,'highlights'=>"Mục tiêu một\nMục tiêu hai",'facts'=>'198 bài học','curriculum_topics'=>'Phần một','is_visible'=>1];
        $this->actingAs($admin)->get('/admin/website')->assertOk()->assertSee('Quản lý khóa học & video',false);
        $this->patch(route('website.courses.update',$course),$data)->assertRedirect(route('website.index'));
        $this->assertSame('Listening đã chỉnh sửa',$course->fresh()->title);$this->assertSame(700000,$course->fresh()->metadata['price']);$this->get('/khoa-hoc/course-15')->assertNotFound();
        $this->post('/admin/website/import')->assertRedirect();
        $this->assertSame('Listening đã chỉnh sửa',$course->fresh()->title);
        unset($data['is_visible']); $this->patch(route('website.courses.update',$course),$data)->assertRedirect();
        $this->get('/khoa-hoc/course-15')->assertRedirect(route('shop.show',Course::where('catalog_key','course-15')->sole()->slug));
        $this->get('/courses/online')->assertDontSee('Listening đã chỉnh sửa');
        $this->assertDatabaseCount('reference_pages',24); $this->assertDatabaseCount('public_teachers',3);
    }
    public function test_admin_edits_teacher_and_home_and_changes_are_public(): void
    {
        $teacher=PublicTeacher::first();$this->actingAs(User::factory()->admin()->create());
        $data=['name'=>'Tên hồ sơ cập nhật','slug'=>$teacher->slug,'bio'=>'Giới thiệu mới','provider'=>'PREP','source_url'=>$teacher->source_url,'qualifications'=>'Chuyên môn đã cập nhật','position'=>8,'is_active'=>1];
        $this->patch(route('website.teachers.update',$teacher),$data)->assertRedirect();
        $this->get('/')->assertSee('Tên hồ sơ cập nhật');
        $this->patch(route('website.home.update'),['title'=>'Trang chủ đã chỉnh trong admin','description'=>'Mô tả mới','teacher_title'=>'Khu vực giáo viên','teacher_description'=>'Thông tin mới'])->assertRedirect();
        $this->get('/')->assertSee('Trang chủ đã chỉnh trong admin')->assertSee('Khu vực giáo viên');
        unset($data['is_active']);$this->patch(route('website.teachers.update',$teacher),$data)->assertRedirect();
        $this->get(route('public_teachers.show',$teacher->slug))->assertNotFound();
    }
    public function test_admin_can_upload_a_real_image_and_invalid_uploads_are_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $data=['name'=>'Giáo viên trung tâm','slug'=>'giao-vien-trung-tam','bio'=>'Hồ sơ thật do quản trị viên cung cấp','provider'=>'English Center','position'=>4,'is_active'=>1];
        $this->post(route('website.teachers.store'),$data+['image'=>UploadedFile::fake()->createWithContent('portrait.png',file_get_contents(public_path('images/teachers/prep-12.png')))])->assertRedirect(route('website.index'));
        $teacher=PublicTeacher::where('slug','giao-vien-trung-tam')->sole();$this->assertFileExists(public_path($teacher->photo_path));
        $this->get(route('public_teachers.show',$teacher->slug))->assertOk();
        unlink(public_path($teacher->photo_path));
        $this->post(route('website.teachers.store'),array_replace($data,['slug'=>'fake-portrait','image'=>UploadedFile::fake()->createWithContent('file.svg','<svg></svg>')]))->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('public_teachers',['slug'=>'fake-portrait']);
    }
}





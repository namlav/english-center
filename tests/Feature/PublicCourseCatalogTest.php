<?php
namespace Tests\Feature;

use App\Models\{Course,User};
use App\Services\{CourseShop,CourseVideo,Study4CourseCatalog};
use Database\Seeders\{WebsiteContentSeeder,CourseShopSeeder,CoursePresentationSeeder};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PublicCourseCatalogTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp():void { parent::setUp(); $this->seed([WebsiteContentSeeder::class,CourseShopSeeder::class,CoursePresentationSeeder::class]); }

    public function test_catalog_has_real_covers_local_links_and_groups_without_outgoing_provider_links():void
    {
        $this->assertDatabaseCount('courses',21);
        $this->get(route('catalog.index'))->assertOk()->assertSee('Combo đặc biệt')->assertSee('IELTS Academic')->assertSee('TOEIC')
            ->assertSee('images/catalog/study4/course-15.webp')->assertDontSee('href="https://study4.com',false)->assertDontSee('tham khảo');
        foreach(app(Study4CourseCatalog::class)->all() as $record){
            $path=public_path($record['images'][0]['local_path']);
            $this->assertSame('image/webp',getimagesize($path)['mime']);
            $this->assertSame($record['images'][0]['content_sha256'],hash_file('sha256',$path));
            $course=Course::where('catalog_key',$record['source_key'])->sole();
            $this->get(route('course_catalog.show',$record['source_key']))->assertRedirect(route('shop.show',$course->slug));
            $this->get(route('shop.show',$course->slug))->assertOk()->assertSee($course->name)->assertSee($course->thumbnail_path)->assertDontSee('href="https://study4.com',false)->assertDontSee('tham khảo');
        }
    }

    public function test_intro_video_plays_inline_and_does_not_claim_unimported_paid_lessons():void
    {
        $course=Course::where('catalog_key','course-15')->sole();
        $this->get(route('shop.show',$course->slug))->assertOk()->assertSee('youtube-nocookie.com/embed/__nzODTNbjM',false)
            ->assertSee('799.000đ')->assertSee($course->lessons()->count().' bài học đã công bố')
            ->assertDontSee('198 bài học')->assertDontSee('101,575');
        $this->assertTrue(app(CourseShop::class)->available()->whereKey($course->id)->exists());
    }

    public function test_search_category_visibility_and_database_edits_control_public_content():void
    {
        $this->get('/courses/online?q='.urlencode('IELTS Intensive Listening'))->assertOk()->assertSee('IELTS Intensive Listening')->assertDontSee('Combo TOEIC 4 kỹ năng');
        $this->get('/courses/online?category=catalog-toeic')->assertOk()->assertSee('Complete TOEIC')->assertDontSee('IELTS Intensive Listening');
        $this->get('/courses/online?q='.urlencode('<script>alert(1)</script>'))->assertOk()->assertDontSee('<script>alert(1)</script>',false);
        $this->get('/reference-courses?catalog_q=IELTS')->assertRedirect(route('catalog.index',['q'=>'IELTS']));
        $course=Course::where('catalog_key','course-15')->sole();$course->update(['name'=>'Nội dung mới trong database','intro_video_url'=>null]);
        $this->get(route('shop.show',$course->slug))->assertSee('Nội dung mới trong database')->assertDontSee('<iframe',false);
        $course->update(['is_active'=>false]);$this->get(route('shop.show',$course->slug))->assertNotFound();$this->get('/khoa-hoc/course-15')->assertNotFound();
        $this->get('/khoa-hoc/course-999999')->assertNotFound();
    }

    public function test_import_is_repeatable_and_preserves_admin_edits():void
    {
        $course=Course::where('catalog_key','course-15')->sole();$course->update(['name'=>'Tên admin sửa','price'=>123000,'sale_price'=>null,'intro_video_url'=>null,'is_active'=>false]);
        $initialLessons = \App\Models\Lesson::count();
        $this->seed(CoursePresentationSeeder::class);$this->assertDatabaseCount('courses',21);$this->assertSame($initialLessons, \App\Models\Lesson::count());$this->assertDatabaseCount('questions',18);
        $this->assertSame('Tên admin sửa',$course->fresh()->name);$this->assertSame('123000.00',$course->fresh()->price);$this->assertNull($course->fresh()->intro_video_url);
        $this->assertFalse($course->fresh()->is_active);$this->assertDatabaseCount('course_enrollments',0);
        $sample=Course::where('code','EC-TOEIC-READ')->sole();$sample->update(['intro_video_url'=>null]);
        $this->seed(CoursePresentationSeeder::class);$this->assertNull($sample->fresh()->intro_video_url);
    }

    private function adminPayload(Course $c):array {return ['code'=>$c->code,'name'=>$c->name,'slug'=>$c->slug,'learning_category_id'=>$c->learning_category_id,'summary'=>'Mô tả do quản trị viên nhập','description'=>'Thông tin trong database','duration_hours'=>3,'price'=>200000,'access_days'=>90,'is_active'=>1,'published'=>1,'audience'=>'Học sinh mới bắt đầu','outcomes'=>"Mục tiêu một\nMục tiêu hai",'outline'=>"Chủ đề một\nChủ đề hai",'intro_video_url'=>'https://www.youtube.com/watch?v=__nzODTNbjM'];}

    public function test_admin_edits_presentation_and_uploads_local_video_without_html_embedding():void
    {
        $course=Course::where('catalog_key','course-15')->sole();$this->actingAs(User::factory()->admin()->create());
        $this->get(route('shop_admin.courses.edit',$course))->assertOk()->assertSee('value="989000"',false)->assertDontSee('value="989000.00"',false);
        $data=$this->adminPayload($course);
        $this->patch(route('shop_admin.courses.update',$course),$data)->assertSessionHasNoErrors()->assertRedirect();
        $this->get(route('shop.show',$course->slug))->assertSee('Học sinh mới bắt đầu')->assertSee('Chủ đề hai')->assertSee('Mục tiêu hai');
        $this->patch(route('shop_admin.courses.update',$course),$data+['intro_video'=>UploadedFile::fake()->create('intro.mp4',2,'video/mp4')])->assertSessionHasNoErrors();
        $path=$course->fresh()->intro_video_path;$this->assertFileExists(public_path($path));
        try{$this->get(route('shop.show',$course->slug))->assertSee('<video controls',false)->assertSee($path)->assertDontSee('<iframe',false);
            $this->patch(route('shop_admin.courses.update',$course),$data+['remove_video'=>1])->assertSessionHasNoErrors();$this->assertNull($course->fresh()->intro_video_path);
        }finally{unlink(public_path($path));}
        $this->actingAs(User::factory()->student()->create())->patch(route('shop_admin.courses.update',$course),$data)->assertForbidden();
    }

    public function test_untrusted_players_and_non_video_uploads_are_rejected():void
    {
        $course=Course::where('catalog_key','course-15')->sole();$this->actingAs(User::factory()->admin()->create());$data=$this->adminPayload($course);
        foreach(['javascript:alert(1)','https://youtube.com.evil.test/watch?v=__nzODTNbjM','https://youtube.com@evil.test/watch?v=__nzODTNbjM','https://evil.test/a.mp4','http://youtu.be/__nzODTNbjM','https://youtu.be/__nzODTNbjM/extra'] as $url){
            $this->assertNull(CourseVideo::embed($url));$this->patch(route('shop_admin.courses.update',$course),array_replace($data,['intro_video_url'=>$url]))->assertSessionHasErrors('intro_video_url');
        }
        $this->patch(route('shop_admin.courses.update',$course),$data+['intro_video'=>UploadedFile::fake()->createWithContent('evil.mp4','<script>evil</script>')])->assertSessionHasErrors('intro_video');
        $this->assertNull(CourseVideo::local('../../.env'));$this->assertNull(CourseVideo::local('https://evil.test/movie.mp4'));
    }
}


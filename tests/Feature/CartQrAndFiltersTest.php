<?php
namespace Tests\Feature;

use App\Models\{Course,CourseEnrollment,ExamPaper,Order,User};
use Database\Seeders\{WebsiteContentSeeder,CourseShopSeeder};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB,URL};
use Tests\TestCase;

class CartQrAndFiltersTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp():void { parent::setUp(); \App\Models\SiteSetting::where('key','payment_bank')->update(['value'=>['enabled'=>false]]); $this->seed([WebsiteContentSeeder::class,CourseShopSeeder::class]); }
    private function pendingOrder(User $buyer):Order {
        $this->actingAs($buyer)->post(route('cart.add',Course::first()))->assertRedirect(route('cart.index'));
        $checkout=$this->get(route('checkout.form'))->assertOk()->assertSee('Tạo mã QR thanh toán');
        $this->post(route('checkout'),['token'=>$checkout->viewData('token'),'signature'=>$checkout->viewData('signature'),'buyer_name'=>'Người nhận khóa','buyer_email'=>'learner@example.test','total'=>1])->assertRedirect();
        return Order::latest('id')->firstOrFail();
    }
    public function test_guest_course_selection_is_added_to_cart_after_login():void {
        $course=Course::first();$buyer=User::factory()->student()->create(['password'=>'qr-test-password']);
        $this->get(route('catalog.index'))->assertOk()->assertSee('Thêm vào giỏ hàng');
        $this->get(route('cart.select',$course))->assertRedirect(route('login'));
        $this->assertDatabaseCount('cart_items',0);
        $this->post(route('login.post'),['email'=>$buyer->email,'password'=>'qr-test-password'])->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items',['user_id'=>$buyer->id,'course_id'=>$course->id]);
        $this->get(route('catalog.index'))->assertSee('Đã trong giỏ hàng');
    }
    public function test_clear_cart_only_removes_current_buyers_courses_and_invalidates_old_checkout():void {
        $a=User::factory()->student()->create();$b=User::factory()->student()->create();$course=Course::first();
        $this->actingAs($a)->post(route('cart.add',$course));$cart=$this->get(route('cart.index'));$oldToken=$cart->viewData('token');
        $this->actingAs($b)->post(route('cart.add',$course));
        $this->actingAs($a)->delete(route('cart.clear'))->assertRedirect(route('cart.index'));
        $this->assertDatabaseCount('cart_items',1);$this->assertDatabaseHas('cart_items',['user_id'=>$b->id]);
        $this->get(route('cart.index'))->assertSee('Giỏ hàng đang trống');$this->get(route('checkout.form'))->assertRedirect(route('cart.index'));
        $this->post(route('checkout'),['token'=>$oldToken,'signature'=>$cart->viewData('signature')])->assertStatus(419);
    }
    public function test_qr_review_is_private_signed_and_never_pays_on_get():void {
        $buyer=User::factory()->student()->create();$order=$this->pendingOrder($buyer);
        $this->assertSame('Người nhận khóa',$order->buyer_name);$this->assertSame('learner@example.test',$order->buyer_email);$this->assertSame(199000,$order->total);
        $this->assertSame($buyer->email,$buyer->fresh()->email);
        $page=$this->get(route('orders.show',$order->number))->assertOk()->assertSee('data-order-qr',false)->assertDontSee('name="code"',false)->assertDontSee('qr-mascot-window',false);
        preg_match('/data-qr-value="([^"]+)"/',$page->getContent(),$matches);$qrUrl=html_entity_decode($matches[1],ENT_QUOTES);
        $this->get($qrUrl)->assertOk()->assertSee('Xác nhận thanh toán mô phỏng');
        $this->assertSame('pending',$order->fresh()->status);$this->assertDatabaseCount('course_enrollments',0);
        $this->get(route('orders.qr',$order->number))->assertForbidden();
        $this->get($qrUrl.'&changed=1')->assertForbidden();
        $expired=URL::temporarySignedRoute('orders.qr',now()->subMinute(),['order'=>$order->number],absolute:false);$this->get($expired)->assertForbidden();
        $this->actingAs(User::factory()->student()->create())->get($qrUrl)->assertNotFound();
        $this->get(route('orders.status',$order->number))->assertNotFound();
        $this->post(URL::temporarySignedRoute('orders.qr.confirm',now()->addMinutes(10),['order'=>$order->number],absolute:false))->assertNotFound();
    }
    public function test_qr_confirmation_grants_course_once_without_manual_code():void {
        $buyer=User::factory()->student()->create();$order=$this->pendingOrder($buyer);
        $this->get(route('orders.status',$order->number))->assertOk()->assertJsonPath('status','pending');
        $this->post(route('orders.qr.confirm',$order->number))->assertForbidden();
        $url=URL::temporarySignedRoute('orders.qr.confirm',now()->addMinutes(10),['order'=>$order->number],absolute:false);
        $this->post($url,['amount'=>1,'user_id'=>999])->assertRedirect(route('orders.show',$order->number));
        $this->assertSame('paid',$order->fresh()->status);$this->assertDatabaseCount('course_enrollments',1);
        $expiry=CourseEnrollment::sole()->expires_at->toIso8601String();
        $this->post($url)->assertRedirect();$this->assertDatabaseCount('course_enrollments',1);$this->assertSame($expiry,CourseEnrollment::sole()->expires_at->toIso8601String());
        $this->get(route('orders.status',$order->number))->assertJsonPath('status','paid');
        $this->get(route('account.courses'))->assertSee('Ngữ pháp nền tảng')->assertSee('Vào học ngay');
    }
    public function test_exam_filters_combine_skill_category_collection_duration_and_search():void {
        $this->get('/tests?q=TOEIC')->assertViewHas('papers',fn($p)=>$p->total()===1);
        $papers=ExamPaper::orderBy('id')->get();$a=$papers[0];$b=$papers[1];
        $a->update(['title'=>'Bộ nghe đặc biệt','skill'=>'listening','duration_seconds'=>2500]);
        $b->update(['title'=>'Bộ đọc trung bình','duration_seconds'=>1800]);
        $page=$this->get('/tests?skill=listening&duration=over-40&category='.$a->learningCategory->slug.'&q='.urlencode('đặc biệt'))->assertOk()->assertSee('Bộ nghe đặc biệt')->assertDontSee('Bộ đọc trung bình');
        $this->assertSame(1,$page->viewData('papers')->total());
        $this->get('/tests?skill=reading&duration=20-40')->assertSee('Bộ đọc trung bình')->assertDontSee('Bộ nghe đặc biệt');
        $short=$this->get('/tests?format=short')->assertOk()->assertSee('tối đa 20 câu hỏi')->assertDontSee('Bộ nghe đặc biệt');
        $this->assertSame(2,$short->viewData('papers')->total());
        $this->get('/tests?collection=does-not-exist')->assertSee('Chưa tìm thấy bài luyện phù hợp');
        $this->get('/tests?sort=duration')->assertOk()->assertViewHas('papers',fn($p)=>$p->first()->duration_seconds<=1800);
        foreach(['/tests?skill[]=reading','/tests?duration=bad','/tests?sort=bad','/tests?format=bad'] as $url)$this->get($url)->assertRedirect();
        $a->update(['status'=>'draft']);$this->get('/tests?skill=listening')->assertDontSee('Bộ nghe đặc biệt');
    }
    public function test_exam_pagination_keeps_filters():void {
        $paper=ExamPaper::first();
        for($i=0;$i<17;$i++){$copy=$paper->replicate();$copy->title='Đề thêm '.$i;$copy->slug='de-them-'.$i;$copy->save();$section=$copy->sections()->create(['title'=>'Phần 1','position'=>1,'skill'=>'reading']);$group=$section->groups()->create(['title'=>'Nhóm 1','position'=>1]);$section->questions()->create(['question_group_id'=>$group->id,'prompt'=>'Test prompt','type'=>'short_answer','position'=>1,'max_points'=>1]);}
        $page=$this->get('/tests?skill=reading&sort=duration')->assertOk();
        $this->assertStringContainsString('skill=reading',$page->viewData('papers')->nextPageUrl());
        $this->assertStringContainsString('sort=duration',$page->viewData('papers')->nextPageUrl());
        $this->get('/tests?skill=reading&sort=duration&page=2')->assertOk();
    }
}


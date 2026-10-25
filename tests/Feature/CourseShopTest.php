<?php
namespace Tests\Feature;
use App\Models\{Course,CourseEnrollment,ExamPaper,Lesson,Order,User};
use Database\Seeders\{WebsiteContentSeeder,CourseShopSeeder};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
class CourseShopTest extends TestCase {
 use RefreshDatabase;
 protected function setUp():void{parent::setUp(); \App\Models\SiteSetting::where('key','payment_bank')->update(['value'=>['enabled'=>false]]);$this->seed([WebsiteContentSeeder::class,CourseShopSeeder::class]);}
 private function buyer(){return User::factory()->student()->create();}
 private function orderFor(User $user,?Course $course=null):Order {$course??=Course::where('code','EC-GRAMMAR')->sole();$this->actingAs($user)->post(route('cart.add',$course))->assertRedirect(route('cart.index'));$cart=$this->get(route('cart.index'))->assertOk();$this->post(route('checkout'),['token'=>$cart->viewData('token'),'signature'=>$cart->viewData('signature'),'total'=>1])->assertRedirect();return Order::where('user_id',$user->id)->latest('id')->firstOrFail();}
 public function test_published_local_courses_and_real_lessons_are_in_database(){
  $this->assertDatabaseCount('courses',3);$this->assertDatabaseCount('lessons',9);$this->assertDatabaseCount('questions',18);
  $this->get('/courses/online')->assertOk()->assertSee('Ngữ pháp nền tảng')->assertSee('TOEIC Reading cơ bản')->assertDontSee('href="https://study4.com/courses/',false);
  $course=Course::where('code','EC-GRAMMAR')->sole();$lesson=$course->lessons()->first();
  $this->get(route('shop.show',$course->slug))->assertOk()->assertSee('Thêm vào giỏ hàng')->assertSee($lesson->title)->assertDontSee($lesson->content);
  $course->update(['name'=>'Tên đã sửa']);$this->seed(CourseShopSeeder::class);$this->assertSame('Tên đã sửa',$course->fresh()->name);$this->assertDatabaseCount('questions',18);
 }
 public function test_cart_requires_login_and_is_scoped_to_the_buyer(){
  $course=Course::first();$this->post(route('cart.add',$course))->assertRedirect(route('login'));
  $a=$this->buyer();$b=$this->buyer();$this->actingAs($a)->post(route('cart.add',$course));$this->post(route('cart.add',$course));$this->assertDatabaseCount('cart_items',1);
  $this->actingAs($b)->get(route('cart.index'))->assertSee('Giỏ hàng đang trống');$this->delete(route('cart.remove',$course));$this->assertDatabaseCount('cart_items',1);
  $this->actingAs($a)->delete(route('cart.remove',$course));$this->assertDatabaseCount('cart_items',0);
 }
 public function test_checkout_uses_server_prices_and_is_idempotent(){
  $user=$this->buyer();$course=Course::first();$this->actingAs($user)->post(route('cart.add',$course));$cart=$this->get(route('cart.index'));$payload=['token'=>$cart->viewData('token'),'signature'=>$cart->viewData('signature'),'total'=>1,'price'=>1];
  $this->post(route('checkout'),$payload)->assertRedirect();$this->post(route('checkout'),$payload)->assertRedirect();$this->assertDatabaseCount('orders',1);$this->assertDatabaseCount('payments',1);$this->assertDatabaseCount('cart_items',0);
  $order=Order::first();$this->assertSame(199000,$order->total);$this->assertSame('pending',$order->status);$this->assertDatabaseCount('course_enrollments',0);
 }
 public function test_price_changes_or_forged_cart_signature_require_review(){
  $user=$this->buyer();$course=Course::first();$this->actingAs($user)->post(route('cart.add',$course));$cart=$this->get(route('cart.index'));$course->update(['price'=>210000]);
  $this->post(route('checkout'),['token'=>$cart->viewData('token'),'signature'=>$cart->viewData('signature')])->assertSessionHasErrors('cart');$this->assertDatabaseCount('orders',0);
  $cart=$this->get(route('cart.index'));$this->post(route('checkout'),['token'=>$cart->viewData('token'),'signature'=>str_repeat('0',64)])->assertSessionHasErrors('cart');
 }
 public function test_fake_payment_code_grants_once_and_other_users_cannot_confirm(){
  $user=$this->buyer();$order=$this->orderFor($user);$code=$order->payment->reference;
  $this->post(route('orders.confirm',$order->number),['code'=>'WRONG'])->assertSessionHasErrors('code');$this->assertDatabaseCount('course_enrollments',0);
  $this->actingAs($this->buyer())->get(route('orders.show',$order->number))->assertNotFound();$this->post(route('orders.confirm',$order->number),['code'=>$code])->assertNotFound();
  $this->actingAs($user)->post(route('orders.confirm',$order->number),['code'=>$code])->assertRedirect();$this->assertSame('paid',$order->fresh()->status);$this->assertSame('demo',$order->payment->method);$expires=CourseEnrollment::sole()->expires_at->toIso8601String();
  $this->travel(1)->hours();$this->post(route('orders.confirm',$order->number),['code'=>$code])->assertRedirect();$this->assertDatabaseCount('course_enrollments',1);$this->assertSame($expires,CourseEnrollment::sole()->expires_at->toIso8601String());
  $this->get(route('account.courses'))->assertOk()->assertSee('Vào học ngay');$this->get(route('orders.show',$order->number))->assertSee('giao dịch mô phỏng');
 }
 public function test_lessons_require_current_purchase_and_completion_is_private(){
  $user=$this->buyer();$course=Course::first();$lesson=$course->lessons()->first();$order=$this->orderFor($user,$course);
  $this->get(route('learn.lesson',[$course->slug,$lesson->id]))->assertForbidden();
  $this->post(route('orders.confirm',$order->number),['code'=>$order->payment->reference]);
  $this->get(route('learn.lesson',[$course->slug,$lesson->id]))->assertOk()->assertSee($lesson->content);
  $this->post(route('learn.complete',[$course->slug,$lesson->id]))->assertRedirect();$this->assertDatabaseHas('lesson_progress',['user_id'=>$user->id,'lesson_id'=>$lesson->id]);
  $other=Course::where('id','!=',$course->id)->first();$this->get(route('learn.lesson',[$course->slug,$other->lessons()->first()->id]))->assertForbidden();
  CourseEnrollment::sole()->update(['expires_at'=>now()->subMinute(),'started_at'=>now()->subDays(2)]);$this->get(route('learn.lesson',[$course->slug,$lesson->id]))->assertForbidden();
 }
 public function test_admin_can_add_course_lesson_and_publish_without_external_content(){
  $this->actingAs(User::factory()->admin()->create());$category=Course::first()->learning_category_id;
  $data=['code'=>'EC-NEW','name'=>'Khóa admin tạo','slug'=>'khoa-admin-tao','learning_category_id'=>$category,'summary'=>'Giới thiệu khóa mới','description'=>'Nội dung riêng','duration_hours'=>2,'price'=>100000,'access_days'=>30,'is_active'=>1,'published'=>1];
  $this->post(route('shop_admin.courses.store'),$data)->assertRedirect();$course=Course::where('code','EC-NEW')->sole();
  $this->post(route('shop_admin.lessons.store',$course),['title'=>'Bài học thật','slug'=>'bai-hoc-that','content'=>'Nội dung do admin nhập trong database.','position'=>1,'duration_seconds'=>600,'is_published'=>1])->assertRedirect();
  $this->patch(route('shop_admin.courses.update',$course),$data+['is_for_sale'=>1])->assertRedirect();$this->get(route('catalog.index'))->assertSee('Khóa admin tạo');
  $this->actingAs($this->buyer())->get(route('shop_admin.courses.edit',$course))->assertForbidden();
 }
 public function test_admin_can_create_an_exam_with_options_answers_and_publish_it(){
  $this->actingAs(User::factory()->admin()->create());$data=['title'=>'Đề admin tạo','slug'=>'de-admin-tao','learning_category_id'=>Course::first()->learning_category_id,'skill'=>'reading','duration_seconds'=>600,'status'=>'draft'];
  $this->post(route('exam_admin.store'),$data)->assertRedirect();$paper=ExamPaper::where('slug','de-admin-tao')->sole();
  $this->post(route('exam_admin.questions.store',$paper),['prompt'=>'She ___ a teacher.','type'=>'single_choice','position'=>1,'max_points'=>1,'options'=>['A'=>'is','B'=>'are'],'answers'=>'A','explanation'=>'She đi với is.'])->assertRedirect();
  $this->patch(route('exam_admin.update',$paper),array_replace($data,['status'=>'published']))->assertRedirect();
  $this->get(route('practice.show',$paper->slug))->assertOk()->assertSee('She ___ a teacher.')->assertDontSee('She đi với is.');
  $q=$paper->questions()->sole();$this->post(route('practice.check',$paper->slug),['answers'=>[$q->id=>['A']]])->assertOk()->assertSee('She đi với is.');
 }
 public function test_admin_editor_pages_and_order_views_render(){
  $user=User::factory()->admin()->create();$order=$this->orderFor($user);$course=Course::first();$paper=ExamPaper::first();
  foreach([route('shop_admin.index'),route('shop_admin.courses.create'),route('shop_admin.courses.edit',$course),route('shop_admin.lessons.create',$course),route('shop_admin.lessons.edit',[$course,$course->lessons()->first()]),route('shop_admin.orders.index'),route('shop_admin.orders.show',$order->number),route('orders.index'),route('exam_admin.index'),route('exam_admin.create'),route('exam_admin.edit',$paper),route('exam_admin.questions.create',$paper),route('exam_admin.questions.edit',[$paper,$paper->questions()->first()])] as $url)$this->get($url)->assertOk();
 }
 public function test_pending_orders_prevent_deleting_a_buyer_or_losing_history(){
  $buyer=$this->buyer();$order=$this->orderFor($buyer);$this->actingAs(User::factory()->admin()->create())->delete(route('users.destroy',$buyer))->assertRedirect(route('users.index'))->assertSessionHas('error');$this->assertDatabaseHas('users',['id'=>$buyer->id]);$this->assertDatabaseHas('orders',['id'=>$order->id]);
 }
 public function test_unupgraded_store_has_a_clear_setup_screen(){
  Schema::drop('payments');Schema::drop('order_items');Schema::drop('orders');
  $this->actingAs(User::factory()->admin()->create())->get(route('shop_admin.index'))->assertOk()->assertSee('Cửa hàng đang được cập nhật');$this->get(route('orders.index'))->assertOk()->assertSee('45 bảng');$this->get(route('shop_admin.upgrade'))->assertDownload('CAP_NHAT_QR_NGAN_HANG.sql');
 }
}


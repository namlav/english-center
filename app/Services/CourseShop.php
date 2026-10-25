<?php
namespace App\Services;
use App\Models\{Course,CourseEnrollment,Order,Payment,User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseShop {
 public const COMBO_MAP = [
  'series-7' => ['course-28', 'course-36'],
  'series-2' => ['course-15', 'course-22', 'course-27', 'course-25'],
  'series-8' => ['course-26', 'course-15', 'course-22', 'course-27', 'course-25'],
  'series-1' => ['course-12', 'course-13', 'course-14'],
 ];

 public function available(){return Schema::hasTable('orders')&&Schema::hasColumn('courses','is_for_sale')?Course::publishedOnline()->where('is_for_sale',true)->whereHas('lessons',fn($q)=>$q->where('is_published',true)):Course::whereRaw('1=0');}
 public function price(Course $c):int{return (int)explode('.',(string)($c->sale_price??$c->price))[0];}

 public function parentComboCourseIds(int $courseId): array {
  $catKey = Course::where('id', $courseId)->value('catalog_key');
  if (!$catKey) return [];
  $parentKeys = [];
  foreach (self::COMBO_MAP as $comboKey => $included) {
   if (in_array($catKey, $included, true)) $parentKeys[] = $comboKey;
  }
  if (empty($parentKeys)) return [];
  return Course::whereIn('catalog_key', $parentKeys)->pluck('id')->all();
 }

 public function comboIncludedCourseIds(int $comboCourseId): array {
  $catKey = Course::where('id', $comboCourseId)->value('catalog_key');
  if (!$catKey || !isset(self::COMBO_MAP[$catKey])) return [];
  return Course::whereIn('catalog_key', self::COMBO_MAP[$catKey])->pluck('id')->all();
 }

 public function owned(User $u,int $id):bool{
  $directly = $u->courseEnrollments()->where('course_id',$id)->where('status','active')->where('started_at','<=',now())->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>',now()))->exists();
  if ($directly) return true;
  $parents = $this->parentComboCourseIds($id);
  if (!empty($parents)) {
   return $u->courseEnrollments()->whereIn('course_id', $parents)->where('status','active')->where('started_at','<=',now())->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>',now()))->exists();
  }
  return false;
 }

 public function cart(User $u){return Schema::hasTable('cart_items')?Course::whereIn('id',DB::table('cart_items')->where('user_id',$u->id)->pluck('course_id'))->orderBy('id')->get():collect();}
 public function signature($courses):string{return hash_hmac('sha256',json_encode($courses->map(fn($c)=>[$c->id,$this->price($c),$c->access_days])->values()->all()),config('app.key'));}

 public function checkout(User $u,string $token,string $signature,array $buyer=[]):Order {
  return DB::transaction(function()use($u,$token,$signature,$buyer){
   User::whereKey($u->id)->lockForUpdate()->firstOrFail();
   if($existing=Order::where('user_id',$u->id)->where('checkout_token',$token)->first())return $existing;
   $ids=DB::table('cart_items')->where('user_id',$u->id)->orderBy('course_id')->pluck('course_id');
   $courses=Course::whereIn('id',$ids)->orderBy('id')->lockForUpdate()->get();
   if($courses->isEmpty())throw ValidationException::withMessages(['cart'=>'Giỏ hàng đang trống.']);
   if(!hash_equals($this->signature($courses),$signature))throw ValidationException::withMessages(['cart'=>'Giá hoặc thời hạn khóa học đã thay đổi. Vui lòng kiểm tra lại giỏ hàng.']);
   foreach($courses as $course)if(!$this->available()->whereKey($course->id)->exists()||$this->owned($u,$course->id)||$this->price($course)<=0)throw ValidationException::withMessages(['cart'=>'Một khóa học không còn khả dụng hoặc bạn đã có quyền học. Hãy cập nhật giỏ hàng.']);
   $bank=app(BankQr::class);$bankMode=$bank->enabled();if($bankMode)$bank->validatedSettings();
   $order=Order::create(['number'=>(string)Str::uuid(),'checkout_token'=>$token,'user_id'=>$u->id,'buyer_name'=>$buyer['buyer_name']??$u->name,'buyer_email'=>$buyer['buyer_email']??$u->email,'total'=>$courses->sum(fn($c)=>$this->price($c)),'status'=>'pending']);
   foreach($courses as $course)$order->items()->create(['course_id'=>$course->id,'course_name'=>$course->name,'unit_price'=>$this->price($course),'access_days'=>$course->access_days]);
   $order->payment()->create(['reference'=>($bankMode?'BANK-':'DEMO-').strtoupper(Str::random(20)),'amount'=>$order->total,'method'=>$bankMode?'bank_transfer':'demo','status'=>'pending']);
   if($bankMode)$bank->create($order);
   DB::table('cart_items')->where('user_id',$u->id)->whereIn('course_id',$ids)->delete();return $order;
  },5);
 }

 public function confirm(Order $order,string $code,User $actor):Order {
  return DB::transaction(function()use($order,$code,$actor){
   User::whereKey($order->user_id)->lockForUpdate()->firstOrFail();
   $order=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();$payment=$order->payment()->lockForUpdate()->firstOrFail();
   abort_if($payment->method==='bank_transfer'&&!$actor->isAdmin(),403);
   if(!hash_equals($payment->reference,strtoupper(trim($code))))throw ValidationException::withMessages(['code'=>'Mã mô phỏng không đúng với đơn hàng này.']);
   if($order->status==='paid')return $order;
   foreach($order->items as $item){
    $targetCourseIds = array_unique(array_merge([$item->course_id], $this->comboIncludedCourseIds($item->course_id)));
    foreach($targetCourseIds as $cId){
     $enrollment=CourseEnrollment::where('user_id',$order->user_id)->where('course_id',$cId)->lockForUpdate()->first();
     if($enrollment&&$enrollment->isCurrent())continue;
     CourseEnrollment::updateOrCreate(['user_id'=>$order->user_id,'course_id'=>$cId],['source'=>'purchase','status'=>'active','started_at'=>now(),'expires_at'=>now()->addDays($item->access_days),'completed_at'=>null]);
    }
   }
   $payment->update(['status'=>'paid','confirmed_by'=>$actor->id,'confirmed_at'=>now()]);$order->update(['status'=>'paid','paid_at'=>now()]);return $order;
  },5);
 }
}

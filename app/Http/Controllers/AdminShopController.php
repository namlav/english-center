<?php
namespace App\Http\Controllers;
use App\Models\{Course,CourseSection,Lesson,LearningCategory,ExamPaper,Order};
use App\Services\CourseShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,File,Schema};
use Illuminate\Validation\Rule;
class AdminShopController extends Controller {
 public function bankSettings(){return view('learning.shop.admin.bank',['bank'=>app(\App\Services\BankQr::class)->settings()]);}
 public function saveBankSettings(Request $r){$d=$r->validate(['bank_bin'=>['required','regex:/^[0-9]{6}$/'],'bank_name'=>['required','string','max:100'],'account_number'=>['required','regex:/^[A-Za-z0-9]{1,19}$/'],'account_name'=>['required','string','max:100','regex:/^[A-Za-z0-9 ]+$/']]);$d['enabled']=true;$d['account_name']=strtoupper(trim($d['account_name']));\App\Models\SiteSetting::updateOrCreate(['key'=>'payment_bank'],['value'=>$d]);return back()->with('success','Đã lưu thông tin ngân hàng. Các QR đã phát hành giữ nguyên tài khoản nhận.');}
 public function upgrade(){return response()->download(database_path('sql/CAP_NHAT_QR_NGAN_HANG.sql'));}
 public function index(){if(!Schema::hasTable('orders')||!Schema::hasColumn('courses','intro_video_url'))return view('learning.shop.setup');return view('learning.shop.admin.courses',['courses'=>Course::where('delivery_mode','online')->withCount('lessons')->orderByDesc('id')->get()]);}
 public function create(){return $this->form(new Course(['is_active'=>true,'access_days'=>365,'duration_hours'=>10,'price'=>199000]));}
 public function edit(Course $course){return $this->form($course);}
 private function form(Course $course){$course->load('sections.lessons');return view('learning.shop.admin.course',['course'=>$course,'categories'=>LearningCategory::where('is_active',true)->get()]);}
 public function store(Request $r){return $this->save($r,new Course);}
 public function update(Request $r,Course $course){return $this->save($r,$course);}
 private function save(Request $r,Course $course){
  $d=$r->validate(['code'=>['required','string','max:30','regex:/^[A-Za-z0-9_-]+$/',Rule::unique('courses','code')->ignore($course->id)],'name'=>['required','string','max:255'],'slug'=>['required','string','max:150','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',Rule::unique('courses','slug')->ignore($course->id)],'learning_category_id'=>['required','exists:learning_categories,id'],'summary'=>['required','string','max:5000'],'description'=>['nullable','string','max:30000'],'duration_hours'=>['required','integer','between:1,10000'],'price'=>['required','integer','between:1,100000000'],'sale_price'=>['nullable','integer','min:1','lte:price'],'access_days'=>['required','integer','between:1,3650'],'outcomes'=>['nullable','string','max:5000'],'is_active'=>['nullable','boolean'],'published'=>['nullable','boolean'],'is_for_sale'=>['nullable','boolean'],'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:4096']]);
  if($r->boolean('is_for_sale')&&(!$course->exists||!$course->lessons()->where('is_published',true)->exists()))return back()->withInput()->withErrors(['is_for_sale'=>'Thêm ít nhất một bài học đã công bố trước khi mở bán.']);
  $presentation=$r->validate([
   'audience'=>['nullable','string','max:10000'], 'outline'=>['nullable','string','max:20000'],
   'intro_video_url'=>['nullable','string','max:500',function($attribute,$value,$fail){if(!\App\Services\CourseVideo::embed($value))$fail('Nhập đường dẫn video YouTube HTTPS hợp lệ.');}],
   'intro_video'=>['nullable','file','mimetypes:video/mp4','max:102400'],
   'remove_video'=>['nullable','boolean'],
  ]);
  $course->fill(collect($d)->except(['outcomes','published','image'])->all());$course->delivery_mode='online';$course->is_active=$r->boolean('is_active');$course->is_for_sale=$r->boolean('is_for_sale');$course->published_at=$r->boolean('published')?($course->published_at??now()):null;$course->learning_outcomes=array_values(array_filter(array_map('trim',preg_split('/\R/u',$d['outcomes']??''))));
  if($r->hasFile('image')){$f=$r->file('image');File::ensureDirectoryExists(public_path('images/uploads'));$path='images/uploads/'.bin2hex(random_bytes(16)).'.'.$f->extension();File::put(public_path($path),File::get($f->getRealPath()));$course->thumbnail_path=$path;}
  $course->audience=$presentation['audience']??null;
  $course->sale_price=$d['sale_price']??null;
  $course->curriculum_outline=array_values(array_filter(array_map('trim',preg_split('/\R/u',$presentation['outline']??''))));
  $course->intro_video_url=$presentation['intro_video_url']??null;
  if($r->boolean('remove_video')){$course->intro_video_url=null;$course->intro_video_path=null;}
  if($r->hasFile('intro_video')){$f=$r->file('intro_video');File::ensureDirectoryExists(public_path('videos/courses'));$path='videos/courses/'.bin2hex(random_bytes(16)).'.mp4';File::put(public_path($path),File::get($f->getRealPath()));$course->intro_video_path=$path;}
  $course->save();return redirect()->route('shop_admin.courses.edit',$course)->with('success','Đã lưu khóa học. Bạn có thể thêm bài học và mở bán.');
 }
 public function createLesson(Course $course){return $this->lessonForm($course,new Lesson(['is_published'=>true,'type'=>'text','position'=>($course->lessons()->max('lessons.position')??0)+1,'duration_seconds'=>600]));}
 public function editLesson(Course $course,Lesson $lesson){abort_unless($lesson->courseSection->course_id===$course->id,404);return $this->lessonForm($course,$lesson);}
 private function lessonForm(Course $course,Lesson $lesson){return view('learning.shop.admin.lesson',['course'=>$course,'lesson'=>$lesson,'papers'=>ExamPaper::orderBy('title')->get(),'selected'=>$lesson->exists?$lesson->examPapers()->pluck('exam_papers.id')->all():[]]);}
 public function storeLesson(Request $r,Course $course){return $this->saveLesson($r,$course,new Lesson);}
 public function updateLesson(Request $r,Course $course,Lesson $lesson){abort_unless($lesson->courseSection->course_id===$course->id,404);return $this->saveLesson($r,$course,$lesson);}
 private function saveLesson(Request $r,Course $course,Lesson $lesson){
  $section=$course->sections()->firstOrCreate(['position'=>1],['title'=>'Nội dung khóa học']);
  $d=$r->validate(['title'=>['required','string','max:255'],'slug'=>['required','string','max:150','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',Rule::unique('lessons','slug')->where('course_section_id',$lesson->course_section_id??$section->id)->ignore($lesson->id)],'content'=>['required','string','max:100000'],'position'=>['required','integer','min:1',Rule::unique('lessons','position')->where('course_section_id',$lesson->course_section_id??$section->id)->ignore($lesson->id)],'duration_seconds'=>['required','integer','between:0,86400'],'media_path'=>['nullable','string','max:500'],'is_preview'=>['nullable','boolean'],'is_published'=>['nullable','boolean'],'exam_ids'=>['nullable','array','max:20'],'exam_ids.*'=>['integer','distinct','exists:exam_papers,id']]);
  DB::transaction(function()use($r,$course,$section,$lesson,$d){$lesson->fill(collect($d)->except(['exam_ids','is_published','is_preview'])->all());if(!$lesson->exists){$lesson->course_section_id=$section->id;$lesson->type=!empty($d['media_path'])?'video':'text';}elseif(!empty($d['media_path'])){$lesson->type='video';}$lesson->is_published=$r->boolean('is_published');$lesson->is_preview=$r->boolean('is_preview');$lesson->media_path=$d['media_path']??null;$lesson->save();$links=[];foreach($d['exam_ids']??[] as $i=>$id)$links[$id]=['position'=>$i+1,'is_required'=>false];$lesson->examPapers()->sync($links);});
  return redirect()->route('shop_admin.courses.edit',$course)->with('success','Đã lưu bài học và các đề luyện gắn kèm.');
 }
 public function orders(Request $r){if(!Schema::hasTable('orders'))return view('learning.shop.setup');$d=$r->validate(['status'=>['nullable',Rule::in(['pending','paid'])]]);$orders=Order::with('payment')->withCount('items')->when($d['status']??null,fn($q,$s)=>$q->where('status',$s))->latest('id')->paginate(25)->withQueryString();return view('learning.shop.admin.orders',compact('orders'));}
 public function order(Order $order){$order->load(['items','payment']);return view('learning.shop.order',['order'=>$order,'adminView'=>true]);}
 public function confirm(Request $r,Order $order,CourseShop $shop){if($order->payment->method==='bank_transfer')$r->validate(['verified'=>['accepted']]);$shop->confirm($order,$order->payment->reference,$r->user());return back()->with('success','Admin đã xác nhận thanh toán và cấp quyền học.');}
}

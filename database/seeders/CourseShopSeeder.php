<?php
namespace Database\Seeders;
use App\Models\{Course,LearningCategory,ExamPaper};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CourseShopSeeder extends Seeder {
 public function run():void {DB::transaction(function(){foreach(json_decode(file_get_contents(database_path('data/course-shop-samples.json')),true,512,JSON_THROW_ON_ERROR) as $data){
  $category=LearningCategory::firstOrCreate(['slug'=>'ec-'.explode('-',$data['slug'])[0]],['name'=>$data['category'],'is_active'=>true]);
  $course=Course::firstOrCreate(['code'=>$data['code']],['name'=>$data['name'],'slug'=>$data['slug'],'learning_category_id'=>$category->id,'delivery_mode'=>'online','summary'=>$data['summary'],'description'=>$data['summary']."\n\nKhóa mẫu do English Center tự biên soạn. Nội dung học và đề luyện được lưu ngay trong hệ thống. Có ba bài học, bài tập áp dụng và một đề tổng kết gồm sáu câu kèm lời giải. Thanh toán trong bản này là mô phỏng, không thu tiền thật.",'duration_hours'=>3,'price'=>$data['price'],'access_days'=>365,'is_active'=>true,'is_for_sale'=>true,'published_at'=>now(),'learning_outcomes'=>$data['outcomes']]);
  $section=$course->sections()->firstOrCreate(['position'=>1],['title'=>'Lộ trình học và thực hành']);$last=null;
  foreach($data['lessons'] as $i=>$lesson)$last=$section->lessons()->firstOrCreate(['slug'=>$lesson['slug']],$lesson+['position'=>$i+1,'type'=>'text','duration_seconds'=>1200,'is_published'=>true]);
  $paper=ExamPaper::firstOrCreate(['slug'=>$data['exam']['slug']],['learning_category_id'=>$category->id,'title'=>$data['exam']['title'],'description'=>'Đề luyện tự biên soạn, sáu câu có đáp án và giải thích. Không phải đề thi chứng chỉ chính thức.','skill'=>'reading','duration_seconds'=>900,'status'=>'published','published_at'=>now()]);
  $examSection=$paper->sections()->firstOrCreate(['position'=>1],['title'=>'Phần luyện tập','skill'=>'reading','instructions'=>'Đọc kỹ câu hỏi và chọn một đáp án đúng.']);$group=$examSection->groups()->firstOrCreate(['position'=>1],['title'=>'Câu hỏi tổng kết','instructions'=>'Mỗi câu chọn một đáp án.']);
  foreach($data['exam']['questions'] as $i=>[$prompt,$options,$answer,$explanation]){$q=$group->questions()->firstOrCreate(['position'=>$i+1],['exam_section_id'=>$examSection->id,'prompt'=>$prompt,'type'=>'single_choice','max_points'=>1]);foreach($options as $j=>$text)$q->options()->firstOrCreate(['label'=>chr(65+$j)],['content'=>$text,'position'=>$j+1]);$q->answerKey()->firstOrCreate([],['accepted_answers'=>[$answer],'case_sensitive'=>false,'explanation'=>$explanation]);}
  if($last&&!$last->examPapers()->whereKey($paper->id)->exists())$last->examPapers()->attach($paper->id,['position'=>1,'is_required'=>false]);
 }});}
}

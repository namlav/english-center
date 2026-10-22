<?php
namespace App\Http\Controllers;
use App\Models\{ExamPaper,Question,LearningCategory};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\{Rule,ValidationException};
class AdminExamController extends Controller {
 public function index(){return view('learning.shop.admin.exams',['papers'=>ExamPaper::withCount('questions')->orderByDesc('id')->paginate(20)]);}
 public function create(){return $this->form(new ExamPaper(['skill'=>'reading','duration_seconds'=>900,'status'=>'draft']));}
 public function edit(ExamPaper $paper){return $this->form($paper);}
 private function form(ExamPaper $paper){$paper->load('questions');return view('learning.shop.admin.exam',['paper'=>$paper,'categories'=>LearningCategory::where('is_active',true)->get()]);}
 public function store(Request $r){return $this->save($r,new ExamPaper);}
 public function update(Request $r,ExamPaper $paper){return $this->save($r,$paper);}
 private function save(Request $r,ExamPaper $paper){$d=$r->validate(['title'=>['required','string','max:255'],'slug'=>['required','string','max:150','regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',Rule::unique('exam_papers','slug')->ignore($paper->id)],'learning_category_id'=>['required','exists:learning_categories,id'],'description'=>['nullable','string','max:10000'],'skill'=>['required',Rule::in(['reading','listening','writing','speaking','mixed'])],'duration_seconds'=>['required','integer','between:60,18000'],'status'=>['required',Rule::in(['draft','published','archived'])]]);if($d['status']==='published'&&(!$paper->exists||!$paper->questions()->whereHas('answerKey')->exists()))throw ValidationException::withMessages(['status'=>'Thêm câu hỏi và đáp án trước khi công bố đề.']);$paper->fill($d);$paper->created_by=$paper->created_by??$r->user()->id;$paper->published_at=$d['status']==='published'?($paper->published_at??now()):null;$paper->save();return redirect()->route('exam_admin.edit',$paper)->with('success','Đã lưu đề thi.');}
 public function createQuestion(ExamPaper $paper){return $this->questionForm($paper,new Question(['type'=>'single_choice','position'=>($paper->questions()->max('questions.position')??0)+1,'max_points'=>1]));}
 public function editQuestion(ExamPaper $paper,Question $question){abort_unless($question->examSection->exam_paper_id===$paper->id,404);return $this->questionForm($paper,$question);}
 private function questionForm(ExamPaper $paper,Question $question){$question->load(['options','answerKey']);return view('learning.shop.admin.question',compact('paper','question'));}
 public function storeQuestion(Request $r,ExamPaper $paper){return $this->saveQuestion($r,$paper,new Question);}
 public function updateQuestion(Request $r,ExamPaper $paper,Question $question){abort_unless($question->examSection->exam_paper_id===$paper->id,404);return $this->saveQuestion($r,$paper,$question);}
 private function saveQuestion(Request $r,ExamPaper $paper,Question $question){
  $section=$question->exists?$question->examSection:$paper->sections()->firstOrCreate(['position'=>1],['title'=>'Phần luyện tập','skill'=>$paper->skill==='mixed'?'reading':$paper->skill,'instructions'=>'Đọc câu hỏi và chọn hoặc nhập đáp án.']);
  $d=$r->validate(['prompt'=>['required','string','max:10000'],'type'=>['required',Rule::in(['single_choice','multiple_choice','short_answer','true_false'])],'position'=>['required','integer','min:1',Rule::unique('questions','position')->where('exam_section_id',$section->id)->ignore($question->id)],'max_points'=>['required','numeric','between:0.01,1000'],'answers'=>['required','string','max:4000'],'explanation'=>['required','string','max:10000'],'case_sensitive'=>['nullable','boolean'],'options'=>['nullable','array','max:4'],'options.*'=>['nullable','string','max:5000']]);
  $answers=array_values(array_unique(array_filter(array_map('trim',preg_split('/\R/u',$d['answers'])))));
  $options=[];if($d['type']==='true_false')$options=['TRUE'=>'Đúng','FALSE'=>'Sai'];elseif($d['type']!=='short_answer')foreach(['A','B','C','D'] as $label)if(trim($d['options'][$label]??'')!=='')$options[$label]=trim($d['options'][$label]);
  if(!$answers||($d['type']!=='short_answer'&&(count($options)<2||array_diff($answers,array_keys($options))))||($d['type']!=='multiple_choice'&&$d['type']!=='short_answer'&&count($answers)!==1))throw ValidationException::withMessages(['answers'=>'Đáp án phải khớp nhãn lựa chọn. Mỗi đáp án/biến thể nhập một dòng.']);
  DB::transaction(function()use($question,$section,$d,$answers,$options,$r){$group=$question->exists?$question->questionGroup:$section->groups()->firstOrCreate(['position'=>1],['title'=>'Câu hỏi','instructions'=>'Làm các câu hỏi sau.']);$question->fill(collect($d)->only(['prompt','type','position','max_points'])->all());$question->question_group_id=$group->id;$question->exam_section_id=$section->id;$question->save();$question->options()->delete();$pos=1;foreach($options as $label=>$text)$question->options()->create(['label'=>$label,'content'=>$text,'position'=>$pos++]);$question->answerKey()->updateOrCreate([],['accepted_answers'=>$answers,'case_sensitive'=>$r->boolean('case_sensitive'),'explanation'=>$d['explanation'],'rubric'=>null]);});
  return redirect()->route('exam_admin.edit',$paper)->with('success','Đã lưu câu hỏi và đáp án.');
 }
}

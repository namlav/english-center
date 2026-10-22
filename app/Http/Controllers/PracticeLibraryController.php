<?php
namespace App\Http\Controllers;

use App\Models\ExamPaper;
use App\Models\LearningCategory;
use App\Models\ExamCollection;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PracticeLibraryController extends Controller
{
    public function index(Request $request): View
    {
        $skills=['reading'=>'Reading','listening'=>'Listening','speaking'=>'Speaking','writing'=>'Writing','mixed'=>'Tổng hợp'];
        $filters = $request->validate(['q'=>['nullable','string','max:120'],'category'=>['nullable','string','max:100'],'skill'=>['nullable',Rule::in(array_keys($skills))],'duration'=>['nullable',Rule::in(['under-20','20-40','over-40'])],'collection'=>['nullable','string','max:150'],'format'=>['nullable',Rule::in(['all','short'])],'sort'=>['nullable',Rule::in(['newest','oldest','duration','questions'])]]);
        $keyword = trim($filters['q'] ?? ''); $category = $filters['category'] ?? '';
        $categoryName=LearningCategory::where('slug',$category)->where('is_active',true)->value('name');
        $catalogFilters=['keyword'=>$keyword]+$filters;unset($catalogFilters['category']);
        $query = ExamPaper::published()->whereHas('questions')->catalogFilter($catalogFilters)->when($category!=='',fn($q)=>$q->whereHas('learningCategory',fn($c)=>$c->where('name',$categoryName??$category)));
        $duration=$filters['duration']??'';
        if($duration==='under-20')$query->where('duration_seconds','<=',1200);
        elseif($duration==='20-40')$query->whereBetween('duration_seconds',[1201,2400]);
        elseif($duration==='over-40')$query->where('duration_seconds','>',2400);
        if(($filters['format']??'all')==='short')$query->where('duration_seconds','<=',1800)->has('questions','<=',20);
        $query->with(['learningCategory','collection'])->withCount(['sections','questions']);
        match($filters['sort']??'newest'){'oldest'=>$query->orderBy('published_at'),'duration'=>$query->orderBy('duration_seconds'),'questions'=>$query->orderBy('questions_count'),default=>$query->orderByDesc('published_at')};
        $papers=$query->orderBy('id')->paginate(16)->withQueryString()->fragment('practice');
        $categories = LearningCategory::where('is_active', true)->whereHas('examPapers', fn ($query) => $query->published()->whereHas('questions'))->orderBy('name')->get()->unique('name');
        $collections=ExamCollection::where('is_active',true)->whereHas('examPapers',fn($q)=>$q->published()->whereHas('questions'))->orderBy('title')->get();
        return view('learning.tests', compact('papers','categories','keyword','category','categoryName','filters','skills','collections'));
    }
}


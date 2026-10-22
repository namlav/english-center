<?php

namespace App\Http\Controllers;

use App\Models\ExamPaper;
use App\Models\Question;
use App\Models\PublicTeacher;
use App\Models\SiteSetting;
use App\Services\Study4CourseCatalog;
use App\Services\CourseShop;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request, Study4CourseCatalog $courseCatalog): View|RedirectResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:100'],
            'catalog_q' => ['nullable', 'string', 'max:120'],
        ]);
        if ($request->has('catalog_q')) { return redirect()->route('catalog.index', ['catalog_q' => $filters['catalog_q'] ?? '']); }
        if ($request->hasAny(['q','category'])) { return redirect()->route('practice.index', array_intersect_key($filters, array_flip(['q','category']))); }
        $allReferenceCourses = app(CourseShop::class)->available()->withCount(['lessons'=>fn($q)=>$q->where('is_published',true)])->orderBy('id')->get();
        $catalogTotal = \App\Models\Course::publishedOnline()->count();
        $featuredCourses = $allReferenceCourses->take(3);
        $catalog = ExamPaper::published()->whereHas('questions');
        $stats = [
            'papers' => (clone $catalog)->count(),
            'questions' => Question::whereHas('examSection.examPaper', fn ($query) => $query->published())->count(),
        ];
        $featuredPapers = (clone $catalog)
            ->with('learningCategory')->withCount(['sections', 'questions'])
            ->orderByDesc('published_at')->orderBy('id')->limit(3)->get();
        $teachers = Schema::hasTable('public_teachers') ? PublicTeacher::visible()->get() : collect();
        $home = SiteSetting::home();
        return view('learning.portal-home', compact('featuredPapers','featuredCourses','stats','catalogTotal','teachers','home'));
    }
}

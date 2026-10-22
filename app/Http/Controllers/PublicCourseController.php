<?php

namespace App\Http\Controllers;

use App\Services\Study4CourseCatalog;
use Illuminate\View\View;
use Illuminate\Http\Request;

class PublicCourseController extends Controller
{
    public function index(Request $request, Study4CourseCatalog $catalog): \Illuminate\Http\RedirectResponse
    {
        $filters = $request->validate(['catalog_q' => ['nullable','string','max:120']]);
        $catalogKeyword = trim($filters['catalog_q'] ?? '');
        return redirect()->route('catalog.index', ['q'=>$catalogKeyword]);
    }

    public function show(string $key, Study4CourseCatalog $catalog): \Illuminate\Http\RedirectResponse
    {
        $course = \Illuminate\Support\Facades\Schema::hasColumn('courses','catalog_key') ? \App\Models\Course::publishedOnline()->where('catalog_key',$key)->first() : null;
        abort_unless($course, 404);
        return redirect()->route('shop.show', $course->slug);
    }
}

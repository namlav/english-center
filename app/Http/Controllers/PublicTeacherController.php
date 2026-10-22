<?php
namespace App\Http\Controllers;
use App\Models\PublicTeacher;
use Illuminate\View\View;
class PublicTeacherController extends Controller
{
    public function show(PublicTeacher $publicTeacher): View
    {
        abort_unless($publicTeacher->is_active,404);
        return view('learning.teacher',['teacher'=>$publicTeacher]);
    }
}

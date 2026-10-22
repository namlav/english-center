<?php
namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LearningAccountController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load('learnerProfile');
        return view('learning.account.profile', compact('user'));
    }
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required','string','max:255'], 'phone' => ['nullable','string','max:20','regex:/^[0-9+().\s-]+$/'], 'occupation' => ['nullable','string','max:100']]);
        DB::transaction(function () use ($request, $data): void {
            $request->user()->update(['name' => $data['name'], 'phone' => $data['phone'] ?? null]);
            $request->user()->learnerProfile()->updateOrCreate([], ['occupation' => $data['occupation'] ?? null]);
        });
        return redirect()->route('account.show')->with('success', 'Đã lưu thông tin cá nhân.');
    }
    public function courses(Request $request): View
    {
        $user = $request->user();
        $enrollments = $user->courseEnrollments()->with(['course.sections.lessons' => fn ($query) => $query->where('is_published', true)->orderBy('position')])->orderByDesc('started_at')->paginate(9);
        $completedIds = $user->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id')->flip();
        return view('learning.account.courses', compact('user','enrollments','completedIds'));
    }
    public function schedule(Request $request): View
    {
        $user = $request->user();
        $classes = ClassRoom::where(function ($query) use ($user): void {
            $query->whereHas('enrollments.student', fn ($student) => $student->where('user_id', $user->id))
                ->orWhereHas('teacher', fn ($teacher) => $teacher->where('user_id', $user->id));
        })->with(['course','teacher.user'])->orderByDesc('start_date')->paginate(12);
        return view('learning.account.schedule', compact('user','classes'));
    }
}

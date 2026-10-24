<?php
namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LearningAccountController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load(['learnerProfile', 'student']);
        if ($user->isStudent() && ! $user->student) {
            $maxId = (int) \App\Models\Student::max('id');
            $code = sprintf('HV%04d', $maxId + 1);
            while (\App\Models\Student::where('student_code', $code)->exists()) {
                $maxId++;
                $code = sprintf('HV%04d', $maxId + 1);
            }
            $user->student()->create(['student_code' => $code]);
            $user->load('student');
        }

        return view('learning.account.profile', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+().\s-]+$/'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $data): void {
            $user = $request->user();
            $user->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
            ]);
            $user->learnerProfile()->updateOrCreate([], ['occupation' => $data['occupation'] ?? null]);

            if ($user->isStudent() || $user->student) {
                $student = $user->student;
                if (! $student) {
                    $maxId = (int) \App\Models\Student::max('id');
                    $code = sprintf('HV%04d', $maxId + 1);
                    while (\App\Models\Student::where('student_code', $code)->exists()) {
                        $maxId++;
                        $code = sprintf('HV%04d', $maxId + 1);
                    }
                    $student = $user->student()->create(['student_code' => $code]);
                }
                $student->update([
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);
            }
        });

        return redirect()->route('account.show')->with('success', 'Đã lưu thông tin cá nhân và hồ sơ học viên thành công.');
    }
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        if (!Hash::check($validated['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('account.show')->with('success', 'Đổi mật khẩu thành công!');
    }
    public function courses(Request $request): View
    {
        $user = $request->user();
        $enrollments = $user->courseEnrollments()->with(['course.sections.lessons' => fn ($query) => $query->where('is_published', true)->orderBy('position')])->orderByDesc('started_at')->paginate(9);
        $completedIds = $user->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id')->flip();
        $pendingOrders = \App\Models\Order::where('user_id', $user->id)->where('status', 'pending')->with(['items', 'payment'])->latest('id')->get();
        return view('learning.account.courses', compact('user','enrollments','completedIds','pendingOrders'));
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

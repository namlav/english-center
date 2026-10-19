<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function index(Request $request, ClassRoom $classRoom)
    {
        $this->authorizeClass($classRoom);
        $date = $request->query('attendance_date', now()->toDateString());
        validator(['attendance_date' => $date], ['attendance_date' => ['required', 'date']])->validate();

        $classRoom->load(['course', 'enrollments.student.user']);
        $attendance = $classRoom->attendances()->whereDate('attendance_date', $date)->get()->keyBy('student_id');

        return view('attendances.index', compact('classRoom', 'date', 'attendance'));
    }

    public function store(Request $request, ClassRoom $classRoom)
    {
        $this->authorizeClass($classRoom);
        $validated = $request->validate([
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', 'in:present,absent,late'],
            'attendance.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $studentIds = $classRoom->enrollments()->pluck('student_id')->all();
        $submittedIds = array_map('intval', array_keys($validated['attendance']));
        if (array_diff($studentIds, $submittedIds) || array_diff($submittedIds, $studentIds)) {
            throw ValidationException::withMessages(['attendance' => 'Danh sách điểm danh đã thay đổi. Hãy tải lại trang và thử lại.']);
        }

        DB::transaction(function () use ($classRoom, $validated) {
            foreach ($validated['attendance'] as $studentId => $entry) {
                $classRoom->attendances()->updateOrCreate(
                    ['student_id' => $studentId, 'attendance_date' => $validated['attendance_date']],
                    ['status' => $entry['status'], 'note' => $entry['note'] ?? null],
                );
            }
        });

        return redirect()->route('attendances.index', ['classRoom' => $classRoom, 'attendance_date' => $validated['attendance_date']])
            ->with('success', 'Đã lưu điểm danh cho lớp.');
    }

    private function authorizeClass(ClassRoom $classRoom): void
    {
        $user = Auth::user();
        if ($user->isTeacher()) {
            abort_unless($user->teacher && $classRoom->teacher_id === $user->teacher->id, 403);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Attendance;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentController extends Controller
{
    public function enroll(Request $request, ClassRoom $classRoom)
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Tài khoản chưa được liên kết với hồ sơ học viên.');

        $message = DB::transaction(function () use ($classRoom, $student) {
            $lockedClass = ClassRoom::query()->lockForUpdate()->findOrFail($classRoom->id);

            if (! in_array($lockedClass->status, ['upcoming', 'ongoing'], true)) {
                throw ValidationException::withMessages(['class' => 'Lớp này hiện không nhận đăng ký.']);
            }

            if (Enrollment::where('class_room_id', $lockedClass->id)->where('student_id', $student->id)->exists()) {
                throw ValidationException::withMessages(['class' => 'Bạn đã đăng ký lớp này rồi.']);
            }

            if ($lockedClass->enrollments()->count() >= $lockedClass->max_students) {
                throw ValidationException::withMessages(['class' => 'Lớp đã đủ số lượng học viên.']);
            }

            Enrollment::create([
                'class_room_id' => $lockedClass->id,
                'student_id' => $student->id,
                'enrollment_date' => now()->toDateString(),
                'paid_tuition' => $lockedClass->tuition_fee,
                'payment_status' => 'paid',
                'result_status' => 'studying',
            ]);

            return 'Đăng ký lớp học thành công.';
        }, 3);

        return redirect()->route('enrollments.my_classes')->with('success', $message);
    }

    public function myClasses()
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Tài khoản chưa được liên kết với hồ sơ học viên.');

        $enrollments = $student->enrollments()
            ->with(['classRoom.course', 'classRoom.teacher.user'])
            ->latest('enrollment_date')
            ->get();

        $attendanceCounts = Attendance::query()
            ->where('student_id', $student->id)
            ->whereIn('class_room_id', $enrollments->pluck('class_room_id'))
            ->selectRaw("class_room_id, SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count, SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            ->groupBy('class_room_id')
            ->get()
            ->keyBy('class_room_id');

        foreach ($enrollments as $enrollment) {
            $counts = $attendanceCounts->get($enrollment->class_room_id);
            $enrollment->present_count = (int) ($counts?->present_count ?? 0);
            $enrollment->absent_count = (int) ($counts?->absent_count ?? 0);
        }

        return view('enrollments.my_classes', compact('enrollments'));
    }

    public function teacherClasses()
    {
        $user = Auth::user();
        $query = ClassRoom::with(['course', 'teacher.user'])->withCount('enrollments')->latest('start_date');

        if ($user->isTeacher()) {
            abort_unless($user->teacher, 403, 'Tài khoản giáo viên chưa có hồ sơ giáo viên.');
            $query->where('teacher_id', $user->teacher->id);
        }

        $classRooms = $query->paginate(10);

        return view('enrollments.teacher_classes', compact('classRooms'));
    }

    public function classStudents(ClassRoom $classRoom)
    {
        $user = Auth::user();
        if ($user->isTeacher()) {
            abort_unless($user->teacher && $classRoom->teacher_id === $user->teacher->id, 403);
        }

        $classRoom->load(['course', 'teacher.user', 'enrollments.student.user']);
        $attendanceCounts = $classRoom->attendances()
            ->selectRaw('student_id, SUM(status = ?) as present_count, SUM(status = ?) as absent_count, SUM(status = ?) as late_count', ['present', 'absent', 'late'])
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        return view('enrollments.class_students', compact('classRoom', 'attendanceCounts'));
    }
}

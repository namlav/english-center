<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GradeController extends Controller
{
    public function index(ClassRoom $classRoom)
    {
        $this->authorizeClass($classRoom);
        $classRoom->load(['course', 'enrollments.student.user']);

        return view('grades.index', compact('classRoom'));
    }

    public function updateAll(Request $request, ClassRoom $classRoom)
    {
        $this->authorizeClass($classRoom);
        $validated = $request->validate([
            'enrollments' => ['required', 'array'],
            'enrollments.*.midterm_score' => ['nullable', 'numeric', 'between:0,10'],
            'enrollments.*.final_score' => ['nullable', 'numeric', 'between:0,10'],
            'enrollments.*.result_status' => ['required', 'in:studying,passed,failed'],
            'enrollments.*.remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $studentIds = $classRoom->enrollments()->pluck('student_id')->all();
        $submittedIds = array_map('intval', array_keys($validated['enrollments']));
        if (array_diff($studentIds, $submittedIds) || array_diff($submittedIds, $studentIds)) {
            throw ValidationException::withMessages(['enrollments' => 'Danh sách học viên đã thay đổi. Hãy tải lại trang và thử lại.']);
        }

        DB::transaction(function () use ($classRoom, $validated) {
            foreach ($validated['enrollments'] as $studentId => $scores) {
                $classRoom->enrollments()->where('student_id', $studentId)->update([
                    'midterm_score' => $scores['midterm_score'] ?? null,
                    'final_score' => $scores['final_score'] ?? null,
                    'result_status' => $scores['result_status'],
                    'remarks' => $scores['remarks'] ?? null,
                ]);
            }
        });

        return redirect()->route('grades.index', $classRoom)->with('success', 'Đã lưu bảng điểm.');
    }

    public function exportCsv(ClassRoom $classRoom): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorizeClass($classRoom);
        $classRoom->load(['course', 'enrollments.student.user']);

        $filename = 'bang-diem-' . \Illuminate\Support\Str::slug($classRoom->code) . '-' . date('Ymd') . '.csv';

        return response()->streamDownload(function () use ($classRoom) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['STT', 'Mã Học Viên', 'Họ Và Tên', 'Email', 'Số Điện Thoại', 'Điểm Giữa Kỳ', 'Điểm Cuối Kỳ', 'Điểm TB', 'Kết Quả', 'Ghi Chú']);

            $statusLabels = [
                'studying' => 'Đang học',
                'passed' => 'Đạt',
                'failed' => 'Chưa đạt',
            ];

            $i = 1;
            foreach ($classRoom->enrollments as $enrollment) {
                $student = $enrollment->student;
                $midterm = $enrollment->midterm_score !== null ? (float) $enrollment->midterm_score : null;
                $final = $enrollment->final_score !== null ? (float) $enrollment->final_score : null;
                $avg = ($midterm !== null && $final !== null) ? round(($midterm + $final) / 2, 2) : '';

                fputcsv($handle, [
                    $i++,
                    $student->student_code ?? '',
                    $student->user->name ?? '',
                    $student->user->email ?? '',
                    $student->user->phone ?? ($student->phone ?? ''),
                    $midterm !== null ? $midterm : '',
                    $final !== null ? $final : '',
                    $avg,
                    $statusLabels[$enrollment->result_status] ?? $enrollment->result_status,
                    $enrollment->remarks ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function print(ClassRoom $classRoom)
    {
        $this->authorizeClass($classRoom);
        $classRoom->load(['course', 'teacher.user', 'enrollments.student.user']);

        return view('grades.print', compact('classRoom'));
    }

    private function authorizeClass(ClassRoom $classRoom): void
    {
        $user = Auth::user();
        if ($user->isTeacher()) {
            abort_unless($user->teacher && $classRoom->teacher_id === $user->teacher->id, 403);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;

class ReportController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.dashboard');
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $courses = Course::with(['classRooms' => function ($query) {
            $query->withCount('enrollments')
                ->withSum(['enrollments as paid_revenue' => fn ($enrollments) => $enrollments->where('payment_status', 'paid')], 'paid_tuition')
                ->orderBy('name');
        }])->orderBy('name')->get();

        $filename = 'bao-cao-doanh-thu-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($courses) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['STT', 'Khóa Học', 'Mã Lớp', 'Tên Lớp', 'Trạng Thái', 'Học Phí Niêm Yết', 'Sĩ Số Đăng Ký', 'Doanh Thu Thực Thu']);

            $statusLabels = [
                'upcoming' => 'Sắp mở',
                'ongoing' => 'Đang học',
                'completed' => 'Đã kết thúc',
                'cancelled' => 'Đã hủy',
            ];

            $i = 1;
            $totalRevenue = 0;
            $totalStudents = 0;

            foreach ($courses as $course) {
                foreach ($course->classRooms as $classRoom) {
                    $revenue = (float) ($classRoom->paid_revenue ?? 0);
                    $students = (int) $classRoom->enrollments_count;
                    $totalRevenue += $revenue;
                    $totalStudents += $students;

                    fputcsv($handle, [
                        $i++,
                        $course->name,
                        $classRoom->code,
                        $classRoom->name,
                        $statusLabels[$classRoom->status] ?? $classRoom->status,
                        number_format((float) $classRoom->tuition_fee, 0, ',', '.') . ' ₫',
                        $students,
                        number_format($revenue, 0, ',', '.') . ' ₫',
                    ]);
                }
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', 'TỔNG CỘNG', '', '', '', '', $totalStudents, number_format($totalRevenue, 0, ',', '.') . ' ₫']);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

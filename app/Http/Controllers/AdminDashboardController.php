<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ExamPaper;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $hasOrders = Schema::hasTable('orders');
        $hasOrderItems = Schema::hasTable('order_items');
        $hasCourses = Schema::hasTable('courses');
        $hasLessons = Schema::hasTable('lessons');
        $hasExams = Schema::hasTable('exam_papers');
        $hasEnrollments = Schema::hasTable('enrollments');

        // 1. CHỈ SỐ DOANH THU BÁN KHÓA HỌC (TRỌNG TÂM)
        $totalCourseRevenue = $hasOrders ? (int) Order::where('status', 'paid')->sum('total') : 0;
        $paidOrdersCount = $hasOrders ? Order::where('status', 'paid')->count() : 0;
        $pendingOrdersCount = $hasOrders ? Order::where('status', 'pending')->count() : 0;
        $pendingRevenue = $hasOrders ? (int) Order::where('status', 'pending')->sum('total') : 0;
        $totalOrders = $hasOrders ? Order::count() : 0;

        $todayRevenue = $hasOrders ? (int) Order::where('status', 'paid')->whereDate('created_at', today())->sum('total') : 0;
        $thisMonthRevenue = $hasOrders ? (int) Order::where('status', 'paid')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total') : 0;
        $averageOrderValue = $paidOrdersCount > 0 ? (int) round($totalCourseRevenue / $paidOrdersCount) : 0;

        // Doanh thu học phí lớp học offline nếu có
        $offlineRevenue = $hasEnrollments ? (int) Enrollment::where('payment_status', 'paid')->sum('paid_tuition') : 0;
        $totalSystemRevenue = $totalCourseRevenue + $offlineRevenue;

        // 2. BÁO CÁO DOANH THU CHI TIẾT BÁN ĐƯỢC BAO NHIÊU THEO TỪNG KHÓA HỌC
        $courseSales = collect();
        if ($hasOrderItems && $hasOrders) {
            $courseSales = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'paid')
                ->select(
                    'order_items.course_name',
                    DB::raw('COUNT(order_items.id) as total_sold'),
                    DB::raw('SUM(order_items.unit_price) as course_revenue'),
                    DB::raw('MAX(order_items.unit_price) as unit_price')
                )
                ->groupBy('order_items.course_name')
                ->orderByDesc('course_revenue')
                ->get();
        }

        // Bổ sung % doanh thu
        $topCoursesWithRevenue = $courseSales->map(function ($item) use ($totalCourseRevenue) {
            $item->percentage = $totalCourseRevenue > 0 
                ? round(($item->course_revenue / $totalCourseRevenue) * 100, 1) 
                : 0;
            return $item;
        });

        // 3. ĐƠN HÀNG BÁN KHÓA HỌC GẦN ĐÂY CẦN ĐỐI SOÁT
        $recentOrders = $hasOrders 
            ? Order::with(['items', 'payment'])->latest('id')->take(8)->get() 
            : collect();

        // 4. CÁC THÔNG SỐ PHỤ KHÁC (Học viên, Giảng viên, Lớp học, Đề thi, Bài giảng)
        $compactStats = [
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'users' => User::count(),
            'courses' => $hasCourses ? Course::count() : 0,
            'forSaleCourses' => $hasCourses ? Course::where('is_for_sale', true)->count() : 0,
            'lessons' => $hasLessons ? Lesson::count() : 0,
            'exams' => $hasExams ? ExamPaper::count() : 0,
            'classes' => Schema::hasTable('class_rooms') ? ClassRoom::count() : 0,
        ];

        // 5. DỮ LIỆU SƠ ĐỒ & BIỂU ĐỒ TRỰC QUAN (CHARTS & DIAGRAMS)
        // a. Xu hướng doanh thu 7 ngày gần nhất
        $dailyData = collect();
        if ($hasOrders) {
            $dailyData = DB::table('orders')
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->select(
                    DB::raw('DATE(created_at) as order_date'),
                    DB::raw('SUM(total) as daily_revenue'),
                    DB::raw('COUNT(id) as daily_orders')
                )
                ->groupBy('order_date')
                ->get()
                ->keyBy('order_date');
        }

        $chartDates = [];
        $chartDailyRevenues = [];
        $chartDailyOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $dateStr = $dateObj->toDateString();
            $chartDates[] = $dateObj->format('d/m');

            $dayRecord = $dailyData->get($dateStr);
            $chartDailyRevenues[] = $dayRecord ? (int) $dayRecord->daily_revenue : 0;
            $chartDailyOrders[] = $dayRecord ? (int) $dayRecord->daily_orders : 0;
        }

        // b. Tỷ trọng doanh thu theo khóa học cho biểu đồ tròn (Donut Chart)
        $chartCourseLabels = [];
        $chartCourseRevenues = [];
        $otherRevenue = 0;
        foreach ($topCoursesWithRevenue as $idx => $item) {
            if ($idx < 5) {
                $shortName = mb_strlen($item->course_name) > 30 
                    ? mb_substr($item->course_name, 0, 27) . '...' 
                    : $item->course_name;
                $chartCourseLabels[] = $shortName;
                $chartCourseRevenues[] = (int) $item->course_revenue;
            } else {
                $otherRevenue += (int) $item->course_revenue;
            }
        }
        if ($otherRevenue > 0) {
            $chartCourseLabels[] = 'Các khóa học khác';
            $chartCourseRevenues[] = $otherRevenue;
        }

        return view('admin.dashboard', compact(
            'totalCourseRevenue',
            'paidOrdersCount',
            'pendingOrdersCount',
            'pendingRevenue',
            'totalOrders',
            'todayRevenue',
            'thisMonthRevenue',
            'averageOrderValue',
            'offlineRevenue',
            'totalSystemRevenue',
            'topCoursesWithRevenue',
            'recentOrders',
            'compactStats',
            'chartDates',
            'chartDailyRevenues',
            'chartDailyOrders',
            'chartCourseLabels',
            'chartCourseRevenues'
        ));
    }

    public function exportRevenueCsv(): StreamedResponse
    {
        $orders = Order::with(['items', 'payment'])
            ->where('status', 'paid')
            ->latest('id')
            ->get();

        $enrollments = Enrollment::with(['student.user', 'classRoom.course', 'classRoom.teacher.user'])
            ->where('payment_status', 'paid')
            ->latest('id')
            ->get();

        $filename = 'bao-cao-doanh-thu-toan-he-thong-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($orders, $enrollments) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            // 1. PHẦN ĐƠN HÀNG KHÓA HỌC TRỰC TUYẾN (E-LEARNING & COMBO)
            fputcsv($handle, ['=== DOANH THU ĐƠN HÀNG KHÓA HỌC ONLINE (E-LEARNING) ===']);
            fputcsv($handle, ['STT', 'Mã Đơn Hàng', 'Học Viên Mua', 'Email', 'Các Khóa Học Đã Mua', 'Số Khóa', 'Tổng Tiền (VNĐ)', 'Phương Thức', 'Ngày Thanh Toán', 'Trạng Thái']);

            $i = 1;
            $onlineRevenue = 0;

            foreach ($orders as $order) {
                $coursesList = $order->items->pluck('course_name')->implode('; ');
                $onlineRevenue += (int) $order->total;

                fputcsv($handle, [
                    $i++,
                    $order->number,
                    $order->buyer_name,
                    $order->buyer_email,
                    $coursesList,
                    $order->items->count(),
                    number_format($order->total, 0, ',', '.') . ' ₫',
                    $order->payment?->method === 'bank_transfer' ? 'VietQR Chuyển khoản' : 'Mô phỏng thanh toán',
                    $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i'),
                    'Đã thanh toán'
                ]);
            }

            fputcsv($handle, ['', 'TỔNG DOANH THU KHÓA HỌC ONLINE', '', '', '', $orders->sum(fn($o) => $o->items->count()) . ' lượt khóa', number_format($onlineRevenue, 0, ',', '.') . ' ₫', '', '', '']);
            fputcsv($handle, []);

            // 2. PHẦN HỌC PHÍ LỚP HỌC TRỰC TIẾP (OFFLINE)
            fputcsv($handle, ['=== DOANH THU HỌC PHÍ LỚP HỌC TRỰC TIẾP (OFFLINE) ===']);
            fputcsv($handle, ['STT', 'Mã Hóa Đơn', 'Học Viên', 'Mã Học Viên', 'Lớp Học', 'Giảng Viên Phụ Trách', 'Học Phí (VNĐ)', 'Ngày Thu', 'Trạng Thái']);

            $j = 1;
            $offlineRevenue = 0;

            foreach ($enrollments as $enr) {
                $tuition = (float) $enr->invoice_total;
                $offlineRevenue += $tuition;

                fputcsv($handle, [
                    $j++,
                    $enr->invoice_code,
                    $enr->student?->user?->name ?? '—',
                    $enr->student?->student_code ?? '—',
                    $enr->classRoom?->name ?? '—',
                    $enr->classRoom?->teacher?->user?->name ?? '—',
                    number_format($tuition, 0, ',', '.') . ' ₫',
                    $enr->created_at?->format('d/m/Y H:i') ?? '—',
                    'Đã thanh toán'
                ]);
            }

            fputcsv($handle, ['', 'TỔNG HỌC PHÍ LỚP TRỰC TIẾP', '', '', '', '', number_format($offlineRevenue, 0, ',', '.') . ' ₫', '', '']);
            fputcsv($handle, []);

            // 3. TỔNG KẾT TOÀN HỆ THỐNG
            $totalSystemRevenue = $onlineRevenue + $offlineRevenue;
            fputcsv($handle, ['=== TỔNG DOANH THU THỰC THU TOÀN TRUNG TÂM (ONLINE + OFFLINE) ===', number_format($totalSystemRevenue, 0, ',', '.') . ' ₫']);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(['all', 'class_room', 'online_course'])],
            'status' => ['nullable', Rule::in(['paid', 'unpaid'])],
            'day' => ['nullable', 'integer', 'between:1,31'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'total_desc', 'total_asc'])],
        ]);

        $keyword = trim($filters['keyword'] ?? '');
        $type = $filters['type'] ?? 'all';
        $status = $filters['status'] ?? '';
        $sort = $filters['sort'] ?? 'newest';

        $items = new Collection();

        // 1. LẤY HÓA ĐƠN HỌC PHÍ LỚP HỌC TRỰC TIẾP (OFFLINE ENROLLMENTS)
        if ($type === 'all' || $type === 'class_room') {
            $enrQuery = Enrollment::with(['student.user', 'classRoom.course', 'classRoom.teacher.user'])
                ->when($keyword !== '', function (Builder $query) use ($keyword): void {
                    $query->where(function (Builder $search) use ($keyword): void {
                        $search->whereHas('student.user', fn (Builder $user): Builder => $user
                            ->where('name', 'like', '%'.$keyword.'%')
                            ->orWhere('email', 'like', '%'.$keyword.'%'))
                            ->orWhereHas('student', fn (Builder $student): Builder => $student->where('student_code', 'like', '%'.$keyword.'%'))
                            ->orWhereHas('classRoom', fn (Builder $classRoom): Builder => $classRoom
                                ->where('code', 'like', '%'.$keyword.'%')
                                ->orWhere('name', 'like', '%'.$keyword.'%'))
                            ->orWhereHas('classRoom.teacher.user', fn (Builder $teacher): Builder => $teacher->where('name', 'like', '%'.$keyword.'%'));

                        if (preg_match('/^HD[-\s]?(?:\d{4}[-\s]?)?0*(\d+)$/i', $keyword, $matches)) {
                            $search->orWhereKey((int) $matches[1]);
                        }
                    });
                })
                ->when($status !== '', fn (Builder $query): Builder => $query->where('payment_status', $status))
                ->when(! empty($filters['day']), fn (Builder $query): Builder => $query->whereDay('created_at', (int) $filters['day']))
                ->when(! empty($filters['month']), fn (Builder $query): Builder => $query->whereMonth('created_at', (int) $filters['month']))
                ->when(! empty($filters['year']), fn (Builder $query): Builder => $query->whereYear('created_at', (int) $filters['year']));

            $enrollments = $enrQuery->get();

            foreach ($enrollments as $e) {
                $classRoom = $e->classRoom;
                $student = $e->student;
                $studentUser = $student?->user;
                $teacherUser = $classRoom?->teacher?->user;

                $items->push((object) [
                    'id' => $e->id,
                    'raw_id' => $e->id,
                    'type' => 'class_room',
                    'type_label' => 'Lớp học trực tiếp',
                    'type_badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                    'invoice_code' => $e->invoice_code,
                    'created_at' => $e->created_at,
                    'customer_name' => $studentUser?->name ?? '—',
                    'customer_sub' => $student?->student_code ?? ($studentUser?->email ?? '—'),
                    'customer_email' => $studentUser?->email ?? '',
                    'teacher_name' => $teacherUser?->name ?? '—',
                    'teacher_sub' => $classRoom?->teacher?->teacher_code ?? 'GV Trung tâm',
                    'target_name' => $classRoom?->name ?? '—',
                    'target_sub' => ($classRoom?->code ?? '') . ($classRoom?->course ? ' · ' . $classRoom->course->name : ''),
                    'voucher' => 'Không có',
                    'total' => (float) $e->invoice_total,
                    'payment_status' => $e->payment_status,
                    'payment_status_label' => $e->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán',
                    'payment_method' => 'Chuyển khoản / Tiền mặt',
                    'detail_url' => route('invoices.show', $e->id),
                    'can_mark_paid' => $e->payment_status === 'unpaid',
                    'mark_paid_url' => route('invoices.mark_paid', $e->id),
                    'model' => $e,
                ]);
            }
        }

        // 2. LẤY HÓA ĐƠN ĐƠN HÀNG KHÓA HỌC TRỰC TUYẾN (ONLINE COURSE ORDERS)
        if ($type === 'all' || $type === 'online_course') {
            $ordQuery = Order::with(['items', 'payment', 'user.student'])
                ->whereNull('enrollment_id')
                ->when($keyword !== '', function (Builder $query) use ($keyword): void {
                    $query->where(function (Builder $search) use ($keyword): void {
                        $search->where('buyer_name', 'like', '%'.$keyword.'%')
                            ->orWhere('buyer_email', 'like', '%'.$keyword.'%')
                            ->orWhere('number', 'like', '%'.$keyword.'%')
                            ->orWhereHas('user', fn (Builder $u) => $u
                                ->where('name', 'like', '%'.$keyword.'%')
                                ->orWhere('email', 'like', '%'.$keyword.'%'))
                            ->orWhereHas('user.student', fn (Builder $s) => $s->where('student_code', 'like', '%'.$keyword.'%'))
                            ->orWhereHas('items', fn (Builder $it) => $it->where('course_name', 'like', '%'.$keyword.'%'));
                    });
                })
                ->when($status !== '', function (Builder $query) use ($status): Builder {
                    return $status === 'paid' 
                        ? $query->where('status', 'paid') 
                        : $query->where('status', '!=', 'paid');
                })
                ->when(! empty($filters['day']), fn (Builder $query): Builder => $query->whereDay('created_at', (int) $filters['day']))
                ->when(! empty($filters['month']), fn (Builder $query): Builder => $query->whereMonth('created_at', (int) $filters['month']))
                ->when(! empty($filters['year']), fn (Builder $query): Builder => $query->whereYear('created_at', (int) $filters['year']));

            $orders = $ordQuery->get();

            foreach ($orders as $o) {
                $studentCode = $o->user?->student?->student_code;
                $coursesList = $o->items->pluck('course_name')->implode(', ');
                $isPaid = $o->status === 'paid';

                $items->push((object) [
                    'id' => 'ord_'.$o->id,
                    'raw_id' => $o->id,
                    'type' => 'online_course',
                    'type_label' => 'Khóa học Online',
                    'type_badge_class' => 'bg-info-subtle text-info border border-info-subtle',
                    'invoice_code' => $o->number,
                    'created_at' => $o->created_at,
                    'customer_name' => $o->buyer_name ?? ($o->user?->name ?? '—'),
                    'customer_sub' => ($studentCode ? $studentCode . ' · ' : '') . ($o->buyer_email ?? $o->user?->email ?? '—'),
                    'customer_email' => $o->buyer_email ?? $o->user?->email ?? '',
                    'teacher_name' => 'Hệ thống E-Learning',
                    'teacher_sub' => 'Khóa học trực tuyến',
                    'target_name' => $coursesList ?: 'Khóa học trực tuyến',
                    'target_sub' => $o->items->count() . ' khóa học trực tuyến',
                    'voucher' => 'Không có',
                    'total' => (float) $o->total,
                    'payment_status' => $isPaid ? 'paid' : 'unpaid',
                    'payment_status_label' => $isPaid ? 'Đã thanh toán' : 'Chờ xác nhận',
                    'payment_method' => $o->payment?->method === 'bank_transfer' ? '🏦 VietQR Ngân hàng' : 'Mô phỏng thanh toán',
                    'detail_url' => route('shop_admin.orders.show', $o->number),
                    'can_mark_paid' => ! $isPaid,
                    'mark_paid_url' => route('shop_admin.orders.confirm', $o->number),
                    'model' => $o,
                ]);
            }
        }

        // 3. SẮP XẾP DANH SÁCH HỢP NHẤT
        $sortedItems = match ($sort) {
            'oldest' => $items->sortBy(fn ($item) => $item->created_at?->timestamp ?? 0)->values(),
            'total_desc' => $items->sortByDesc('total')->values(),
            'total_asc' => $items->sortBy('total')->values(),
            default => $items->sortByDesc(fn ($item) => $item->created_at?->timestamp ?? 0)->values(),
        };

        // 4. PHÂN TRANG CHUẨN
        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedSlice = $sortedItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $invoices = new LengthAwarePaginator(
            $pagedSlice,
            $sortedItems->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('invoices._results', compact('invoices'))->render(),
                'total' => $invoices->total(),
            ]);
        }

        // 5. THỐNG KÊ TỔNG THỂ HỆ THỐNG
        $totalOffline = Enrollment::count();
        $paidOffline = Enrollment::where('payment_status', 'paid')->count();
        $unpaidOffline = Enrollment::where('payment_status', 'unpaid')->count();
        $offlineRevenue = (float) Enrollment::where('payment_status', 'paid')->sum('paid_tuition');

        $totalOnline = Order::whereNull('enrollment_id')->count();
        $paidOnline = Order::whereNull('enrollment_id')->where('status', 'paid')->count();
        $unpaidOnline = Order::whereNull('enrollment_id')->where('status', '!=', 'paid')->count();
        $onlineRevenue = (float) Order::whereNull('enrollment_id')->where('status', 'paid')->sum('total');

        $summary = [
            'total' => $totalOffline + $totalOnline,
            'paid' => $paidOffline + $paidOnline,
            'unpaid' => $unpaidOffline + $unpaidOnline,
            'revenue' => $offlineRevenue + $onlineRevenue,
            'offline_count' => $totalOffline,
            'online_count' => $totalOnline,
        ];

        // Năm bộ lọc
        $minDate = min(array_filter([
            Enrollment::min('created_at'),
            Order::min('created_at'),
        ]) ?: [now()]);
        $maxDate = max(array_filter([
            Enrollment::max('created_at'),
            Order::max('created_at'),
        ]) ?: [now()]);

        $years = range((int) date('Y', strtotime($maxDate)), (int) date('Y', strtotime($minDate)));

        return view('invoices.index', compact(
            'invoices', 'summary', 'years', 'keyword', 'type', 'status', 'sort'
        ));
    }

    public function show(string|int $invoice): View|\Illuminate\Http\RedirectResponse
    {
        // 1. Kiểm tra nếu là Enrollment ID (số)
        if (is_numeric($invoice)) {
            $enrollment = Enrollment::find((int) $invoice);
            if ($enrollment) {
                $enrollment->load(['student.user', 'classRoom.course', 'classRoom.teacher.user']);
                return view('invoices.show', ['invoice' => $enrollment]);
            }
        }

        // 2. Kiểm tra nếu là Order Number (UUID) hoặc Order ID
        $order = Order::where('number', (string) $invoice)
            ->orWhere('id', is_numeric($invoice) ? (int) $invoice : 0)
            ->first();

        if ($order) {
            return redirect()->route('shop_admin.orders.show', $order->number);
        }

        // 3. Tìm Enrollment theo invoice_code (HD-YYYY-XXXXX)
        if (preg_match('/^HD[-\s]?(?:\d{4}[-\s]?)?0*(\d+)$/i', (string) $invoice, $matches)) {
            $enrollment = Enrollment::find((int) $matches[1]);
            if ($enrollment) {
                $enrollment->load(['student.user', 'classRoom.course', 'classRoom.teacher.user']);
                return view('invoices.show', ['invoice' => $enrollment]);
            }
        }

        abort(404, 'Không tìm thấy hóa đơn.');
    }

    public function markPaid(Request $request, Enrollment $invoice): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $classRoom = $invoice->classRoom;
        $tuition = (float) ($classRoom?->tuition_fee ?? $invoice->paid_tuition);

        $invoice->update([
            'payment_status' => 'paid',
            'paid_tuition' => $tuition > 0 ? $tuition : $invoice->paid_tuition,
            'result_status' => 'studying',
        ]);

        if ($invoice->student?->user_id && $classRoom?->course_id) {
            \App\Models\CourseEnrollment::firstOrCreate(
                ['user_id' => $invoice->student->user_id, 'course_id' => $classRoom->course_id],
                [
                    'source' => 'classroom',
                    'status' => 'active',
                    'started_at' => $classRoom->start_date ? \Carbon\Carbon::parse($classRoom->start_date)->startOfDay() : now(),
                    'expires_at' => $classRoom->expected_end_date ? \Carbon\Carbon::parse($classRoom->expected_end_date)->addMonth() : null,
                ]
            );
        }

        \App\Services\CourseShop::syncEnrollmentOrder($invoice);

        return back()->with('success', 'Đã duyệt thanh toán thành công cho hồ sơ '.$invoice->invoice_code.' ('.number_format($tuition, 0, ',', '.').' ₫). Học viên đã được kích hoạt nhập học chính thức.');
    }
}

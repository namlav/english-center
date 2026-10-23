@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Tổng Quan — Metis Admin')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Bảng Điều Khiển Quản Trị</h2>
        <p class="text-secondary mb-0">Hệ thống quản lý trung tâm tiếng Anh & học trực tuyến E-Learning.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('shop_admin.orders.index') }}" class="btn btn-warning text-dark fw-bold shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-bell-fill"></i>
            <span>Đơn chờ duyệt ({{ $pendingOrdersCount }})</span>
        </a>
        <a href="{{ route('shop_admin.courses.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Thêm khóa học</span>
        </a>
    </div>
</div>

{{-- 4 METRIC KPI CARDS PHONG CÁCH METIS --}}
<div class="row g-3 mb-4">
    <!-- Doanh thu -->
    <div class="col-sm-6 col-xl-3">
        <div class="metis-metric-card h-100">
            <div class="metis-metric-icon bg-success-subtle text-success">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="metis-metric-title">Doanh thu khóa học</div>
            <div class="metis-metric-value text-success">
                {{ number_format($totalRevenue, 0, ',', '.') }}đ
            </div>
            <div class="metis-metric-trend text-secondary">
                <i class="bi bi-check2-circle text-success"></i>
                <span>{{ $paidOrdersCount }} đơn đã thanh toán thành công</span>
            </div>
        </div>
    </div>

    <!-- Đơn hàng -->
    <div class="col-sm-6 col-xl-3">
        <div class="metis-metric-card h-100">
            <div class="metis-metric-icon bg-primary-subtle text-primary">
                <i class="bi bi-cart-check-fill"></i>
            </div>
            <div class="metis-metric-title">Tổng đơn đặt mua</div>
            <div class="metis-metric-value text-primary">
                {{ $totalOrders }} đơn
            </div>
            <div class="metis-metric-trend text-secondary">
                @if($pendingOrdersCount > 0)
                    <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> {{ $pendingOrdersCount }} đơn chờ kích hoạt</span>
                @else
                    <span class="badge bg-success-subtle text-success">Đã đối soát hết</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Học viên -->
    <div class="col-sm-6 col-xl-3">
        <div class="metis-metric-card h-100">
            <div class="metis-metric-icon bg-info-subtle text-info">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="metis-metric-title">Học viên đăng ký</div>
            <div class="metis-metric-value text-dark">
                {{ $totalStudents }} học viên
            </div>
            <div class="metis-metric-trend text-secondary">
                <i class="bi bi-people"></i>
                <span>{{ $totalTeachers }} giảng viên · {{ $totalUsers }} tài khoản</span>
            </div>
        </div>
    </div>

    <!-- Hệ thống LMS -->
    <div class="col-sm-6 col-xl-3">
        <div class="metis-metric-card h-100">
            <div class="metis-metric-icon bg-danger-subtle text-danger">
                <i class="bi bi-play-btn-fill"></i>
            </div>
            <div class="metis-metric-title">Chương trình đào tạo</div>
            <div class="metis-metric-value text-dark">
                {{ $totalCourses }} khóa học
            </div>
            <div class="metis-metric-trend text-secondary">
                <i class="bi bi-collection-play"></i>
                <span>{{ number_format($totalLessons, 0, ',', '.') }} bài học · {{ $totalExams }} bộ đề</span>
            </div>
        </div>
    </div>
</div>

{{-- TÁC VỤ NHANH (QUICK ACTIONS) --}}
<div class="metis-card mb-4">
    <div class="metis-card-header">
        <h5 class="metis-card-title d-flex align-items-center gap-2">
            <i class="bi bi-lightning-charge-fill text-warning"></i>
            <span>Tác vụ quản trị nhanh</span>
        </h5>
        <span class="badge bg-light text-secondary border">1-Click</span>
    </div>
    <div class="metis-card-body p-3">
        <div class="row g-2">
            <div class="col-6 col-md-3">
                <a href="{{ route('shop_admin.courses.create') }}" class="btn btn-outline-primary w-100 py-3 text-start d-flex align-items-center gap-2 rounded-3">
                    <i class="bi bi-journal-plus fs-4"></i>
                    <div>
                        <strong class="d-block text-dark">Thêm Khóa học</strong>
                        <small class="text-secondary">Tạo khóa & video</small>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('exam_admin.create') }}" class="btn btn-outline-success w-100 py-3 text-start d-flex align-items-center gap-2 rounded-3">
                    <i class="bi bi-patch-question-fill fs-4"></i>
                    <div>
                        <strong class="d-block text-dark">Soạn Đề thi</strong>
                        <small class="text-secondary">Ngân hàng đề online</small>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('shop_admin.orders.index', ['status' => 'pending']) }}" class="btn btn-outline-warning w-100 py-3 text-start d-flex align-items-center gap-2 rounded-3">
                    <i class="bi bi-qr-code-scan fs-4 text-warning"></i>
                    <div>
                        <strong class="d-block text-dark">Duyệt Đơn VietQR</strong>
                        <small class="text-secondary">Kích hoạt học ngay</small>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('users.create') }}" class="btn btn-outline-info w-100 py-3 text-start d-flex align-items-center gap-2 rounded-3">
                    <i class="bi bi-person-plus-fill fs-4"></i>
                    <div>
                        <strong class="d-block text-dark">Thêm Tài khoản</strong>
                        <small class="text-secondary">Admin/Giáo viên/HV</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- ĐƠN HÀNG MỚI NHẤT CẦN ĐỐI SOÁT --}}
    <div class="col-lg-7">
        <div class="metis-card h-100 mb-0">
            <div class="metis-card-header">
                <h5 class="metis-card-title d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-primary"></i>
                    <span>Đơn hàng đặt mua gần đây</span>
                </h5>
                <a href="{{ route('shop_admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Mã đơn / Người mua</th>
                            <th>Tổng tiền</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-3">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="ps-3">
                                    <strong class="text-primary font-monospace">{{ $order->number }}</strong>
                                    <div class="text-dark fw-semibold fs-7">{{ $order->buyer_name }}</div>
                                    <small class="text-muted">{{ $order->created_at?->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <strong class="text-dark">{{ number_format($order->total, 0, ',', '.') }}đ</strong>
                                    <div class="text-muted fs-8">{{ $order->payment?->method === 'bank_transfer' ? 'VietQR' : 'Mô phỏng' }}</div>
                                </td>
                                <td class="text-center">
                                    @if($order->status === 'paid')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Đã kích hoạt</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1">Chờ xác nhận</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('shop_admin.orders.show', $order->number) }}" class="btn btn-sm btn-light border">
                                        Duyệt đơn
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Chưa có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- KHÓA HỌC & HỌC VIÊN MỚI --}}
    <div class="col-lg-5">
        <!-- Khóa học nổi bật -->
        <div class="metis-card mb-4">
            <div class="metis-card-header">
                <h5 class="metis-card-title d-flex align-items-center gap-2">
                    <i class="bi bi-book text-success"></i>
                    <span>Khóa học mới cập nhật</span>
                </h5>
                <a href="{{ route('shop_admin.index') }}" class="btn btn-sm btn-outline-secondary">Tất cả</a>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($topCourses as $c)
                    <li class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary-subtle text-primary fw-bold rounded-2 p-2 text-center" style="width: 44px; height: 38px; font-size: 11px;">
                                {{ $c->code }}
                            </div>
                            <div>
                                <strong class="text-dark d-block fs-7 text-truncate" style="max-width: 220px;">{{ $c->name }}</strong>
                                <small class="text-muted">{{ $c->lessons_count }} bài học · {{ number_format($c->sale_price ?? $c->price, 0, ',', '.') }}đ</small>
                            </div>
                        </div>
                        <a href="{{ route('shop_admin.courses.edit', $c) }}" class="btn btn-sm btn-outline-primary">
                            Sửa
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Học viên mới -->
        <div class="metis-card mb-0">
            <div class="metis-card-header">
                <h5 class="metis-card-title d-flex align-items-center gap-2">
                    <i class="bi bi-person-check text-info"></i>
                    <span>Học viên mới đăng ký</span>
                </h5>
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary">Tất cả</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($recentStudents as $st)
                    <li class="list-group-item d-flex align-items-center justify-content-between py-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="metis-avatar" style="width: 32px; height: 32px; font-size: 12px;">
                                {{ mb_strtoupper(mb_substr($st->name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="fw-semibold text-dark fs-7 d-block">{{ $st->name }}</span>
                                <small class="text-muted">{{ $st->email }}</small>
                            </div>
                        </div>
                        <small class="text-muted">{{ $st->created_at?->diffForHumans() }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-3">Chưa có học viên.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

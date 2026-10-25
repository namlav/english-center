@extends('layouts.admin')

@section('title', 'Tổng Quan & Báo Cáo Doanh Thu Khóa Học — Admin')

@section('content')
{{-- TIÊU ĐỀ TRANG & NÚT TÁC VỤ --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success px-2 py-1 fw-bold fs-8 text-uppercase">Báo cáo doanh số LMS</span>
            <span class="text-muted fs-8">Cập nhật trực tiếp theo thời gian thực</span>
        </div>
        <h2 class="fw-bold text-dark mb-1 mt-1">Tổng Quan & Báo Cáo Doanh Thu Khóa Học</h2>
        <p class="text-secondary mb-0 fs-7">Theo dõi chi tiết doanh số bán khóa học E-Learning, doanh thu thực thu và đối soát thanh toán VietQR.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.revenue.export') }}" class="btn btn-success fw-bold shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-excel-fill"></i>
            <span>Xuất Excel Doanh Thu</span>
        </a>
        <a href="{{ route('shop_admin.orders.index', ['status' => 'pending']) }}" class="btn btn-warning text-dark fw-bold shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-qr-code-scan"></i>
            <span>Đơn chờ duyệt ({{ $pendingOrdersCount }})</span>
        </a>
        <a href="{{ route('shop_admin.courses.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Thêm khóa học</span>
        </a>
    </div>
</div>

{{-- DẢI THỐNG KÊ PHỤ NHỎ GỌN (QUY MÔ HỆ THỐNG) --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 bg-white rounded-3 border mb-4 shadow-sm" style="font-size: 13px;">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="text-muted fw-bold text-uppercase fs-8 me-1"><i class="bi bi-sliders me-1"></i> Quy mô hệ thống:</span>
        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-people text-primary me-1"></i> <strong>{{ number_format($compactStats['students']) }}</strong> Học viên</span>
        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-person-video3 text-success me-1"></i> <strong>{{ number_format($compactStats['teachers']) }}</strong> Giảng viên</span>
        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-building text-info me-1"></i> <strong>{{ number_format($compactStats['classes']) }}</strong> Lớp học</span>
        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-journal-bookmark text-danger me-1"></i> <strong>{{ number_format($compactStats['courses']) }}</strong> Khóa học ({{ $compactStats['forSaleCourses'] }} đang bán)</span>
        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-collection-play text-warning me-1"></i> <strong>{{ number_format($compactStats['lessons']) }}</strong> Bài giảng LMS</span>
        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-card-checklist text-secondary me-1"></i> <strong>{{ number_format($compactStats['exams']) }}</strong> Đề thi online</span>
    </div>
    @if($offlineRevenue > 0)
        <div class="text-muted fs-8">
            Học phí lớp truyền thống: <strong class="text-dark">{{ number_format($offlineRevenue, 0, ',', '.') }}đ</strong>
        </div>
    @endif
</div>

{{-- 4 THẺ CHỈ SỐ DOANH THU CHÍNH (HERO REVENUE METRICS) --}}
<div class="row g-3 mb-4">
    <!-- Doanh thu thực thu -->
    <div class="col-sm-6 col-xl-3">
        <div class="metis-metric-card h-100 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="metis-metric-title mb-0">Doanh thu bán khóa học</div>
                <div class="metis-metric-icon bg-success-subtle text-success m-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <div class="metis-metric-value text-success">
                {{ number_format($totalCourseRevenue, 0, ',', '.') }}đ
            </div>
            <div class="metis-metric-trend text-secondary mt-2">
                <i class="bi bi-check-circle-fill text-success me-1"></i>
                <span><strong>{{ $paidOrdersCount }}</strong> đơn đã thanh toán & kích hoạt</span>
            </div>
        </div>
    </div>

    <!-- Doanh thu tháng này & hôm nay -->
    <div class="col-sm-6 col-xl-3">
        <div class="metis-metric-card h-100 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="metis-metric-title mb-0">Doanh thu Tháng {{ date('m/Y') }}</div>
                <div class="metis-metric-icon bg-primary-subtle text-primary m-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
            </div>
            <div class="metis-metric-value text-primary">
                {{ number_format($thisMonthRevenue, 0, ',', '.') }}đ
            </div>
            <div class="metis-metric-trend text-secondary mt-2">
                <i class="bi bi-clock-history text-primary me-1"></i>
                <span>Hôm nay: <strong>{{ number_format($todayRevenue, 0, ',', '.') }}đ</strong></span>
            </div>
        </div>
    </div>

    <!-- Chờ xác nhận chuyển khoản VietQR -->
    <div class="col-sm-6 col-xl-3">
        <div class="metis-metric-card h-100 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="metis-metric-title mb-0">Chờ duyệt / VietQR</div>
                <div class="metis-metric-icon bg-warning-subtle text-warning m-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <div class="metis-metric-value text-warning-emphasis">
                {{ number_format($pendingRevenue, 0, ',', '.') }}đ
            </div>
            <div class="metis-metric-trend text-secondary mt-2">
                @if($pendingOrdersCount > 0)
                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle-fill me-1"></i> {{ $pendingOrdersCount }} đơn cần duyệt ngay</span>
                @else
                    <span class="text-success"><i class="bi bi-check2-all me-1"></i> Đã kích hoạt 100% đơn</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Giá trị trung bình đơn (AOV) & Tổng đơn -->
    <div class="col-sm-6 col-xl-3">
        <div class="metis-metric-card h-100 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="metis-metric-title mb-0">Giá trị TB / Đơn (AOV)</div>
                <div class="metis-metric-icon bg-info-subtle text-info m-0" style="width: 40px; height: 40px; font-size: 1.15rem;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <div class="metis-metric-value text-dark">
                {{ number_format($averageOrderValue, 0, ',', '.') }}đ
            </div>
            <div class="metis-metric-trend text-secondary mt-2">
                <i class="bi bi-receipt me-1"></i>
                <span>Tổng <strong>{{ $totalOrders }}</strong> đơn đặt mua toàn hệ thống</span>
            </div>
        </div>
    </div>
</div>

{{-- SƠ ĐỒ QUY TRÌNH LUỒNG BÁN HÀNG & CHUYỂN ĐỔI KHÓA HỌC (WORKFLOW / FUNNEL DIAGRAM) --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <strong class="text-dark fs-7"><i class="bi bi-diagram-3-fill text-primary me-1"></i> Sơ đồ Quy Trình Bán Khóa Học & Đối Soát VietQR</strong>
            <small class="text-muted d-block">Mô hình luồng giao dịch khép kín từ lúc học viên xem khóa đến khi kích hoạt vào học.</small>
        </div>
        <span class="badge bg-success-subtle text-success border px-2 py-1 fs-8">
            <i class="bi bi-shield-check me-1"></i> Tự động hóa 100%
        </span>
    </div>
    <div class="card-body p-4 bg-light bg-opacity-25">
        <div class="row g-3 align-items-center text-center">
            <!-- Bước 1 -->
            <div class="col-md-3">
                <div class="p-3 bg-white rounded-3 border shadow-xs h-100 position-relative">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="bi bi-collection-play"></i>
                    </div>
                    <span class="badge bg-primary-subtle text-primary mb-1 fs-8">Bước 1: Danh mục</span>
                    <strong class="d-block text-dark fs-7">21 Khóa Học Mở Bán</strong>
                    <small class="text-muted fs-8 d-block mt-1">IELTS, TOEIC, Giao tiếp trên sàn LMS</small>
                </div>
            </div>

            <!-- Bước 2 -->
            <div class="col-md-3">
                <div class="p-3 bg-white rounded-3 border shadow-xs h-100 position-relative">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <span class="badge bg-info-subtle text-info mb-1 fs-8">Bước 2: Chọn mua</span>
                    <strong class="d-block text-dark fs-7">{{ $totalOrders }} Lượt Đặt Đơn</strong>
                    <small class="text-muted fs-8 d-block mt-1">Giỏ hàng & Đăng ký gói học</small>
                </div>
            </div>

            <!-- Bước 3 -->
            <div class="col-md-3">
                <div class="p-3 bg-white rounded-3 border shadow-xs h-100 position-relative">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="bi bi-qr-code"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning mb-1 fs-8">Bước 3: Thanh toán</span>
                    <strong class="d-block text-dark fs-7">Quét Mã VietQR 24/7</strong>
                    <small class="text-muted fs-8 d-block mt-1">Tự động nhận diện nội dung chuyển khoản</small>
                </div>
            </div>

            <!-- Bước 4 -->
            <div class="col-md-3">
                <div class="p-3 bg-white rounded-3 border shadow-xs h-100 position-relative border-success">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="bi bi-unlock-fill"></i>
                    </div>
                    <span class="badge bg-success text-white mb-1 fs-8">Bước 4: Hoàn tất</span>
                    <strong class="d-block text-success fs-7">Kích Hoạt Học Ngay</strong>
                    <small class="text-muted fs-8 d-block mt-1">{{ number_format($totalCourseRevenue, 0, ',', '.') }}đ đã thực thu</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SƠ ĐỒ BIỂU ĐỒ TRỰC QUAN DOANH THU & TỶ TRỌNG (VISUAL CHARTS) --}}
<div class="row g-4 mb-4">
    <!-- Cột trái: Sơ đồ Biểu đồ Doanh thu theo ngày (Area Chart) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <strong class="text-dark fs-7"><i class="bi bi-bar-chart-line-fill text-primary me-1"></i> Sơ đồ Doanh Thu Bán Khóa Học (7 Ngày Gần Nhất)</strong>
                    <small class="text-muted d-block">Xu hướng tăng trưởng doanh thu bán khóa học theo mốc thời gian.</small>
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active" id="btnChartRevenue">
                        <i class="bi bi-currency-dollar me-1"></i> Doanh thu (₫)
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="btnChartOrders">
                        <i class="bi bi-cart me-1"></i> Số đơn hàng
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="height: 310px; position: relative;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Sơ đồ Biểu đồ Tròn Cơ cấu Tỷ trọng Doanh thu (Donut Chart) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <strong class="text-dark fs-7"><i class="bi bi-pie-chart-fill text-success me-1"></i> Cơ Cấu Doanh Thu Theo Khóa Học</strong>
                <small class="text-muted d-block">Tỷ trọng đóng góp doanh số giữa các khóa.</small>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div style="height: 230px; position: relative;" class="my-auto">
                    <canvas id="courseShareChart"></canvas>
                </div>
                <div class="mt-3 pt-2 border-top fs-8 text-center text-muted">
                    <i class="bi bi-info-circle me-1"></i> Tổng cộng <strong>{{ count($topCoursesWithRevenue) }}</strong> khóa học đã phát sinh doanh số thực tế.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BẢNG BÁO CÁO DOANH THU CHI TIẾT THEO TỪNG KHÓA HỌC (THE MAIN FOCUS) --}}
<div class="metis-card mb-4">
    <div class="metis-card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
        <div>
            <h5 class="metis-card-title d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-table text-primary fs-5"></i>
                <span class="fw-bold">Báo Cáo Chi Tiết Doanh Thu Bán Được Theo Từng Khóa Học</span>
            </h5>
            <small class="text-secondary">Chi tiết số lượng khóa học đã bán và tổng số tiền thu về từ mỗi khóa học.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success text-white px-3 py-2 fw-bold fs-7">
                Tổng thực thu: {{ number_format($totalCourseRevenue, 0, ',', '.') }} ₫
            </span>
            <a href="{{ route('admin.revenue.export') }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-download me-1"></i> Xuất CSV
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="fs-8 text-uppercase text-secondary">
                    <th class="ps-3 py-3" style="width: 40px;">#</th>
                    <th>Tên Khóa Học</th>
                    <th class="text-center">Đơn Giá Bán</th>
                    <th class="text-center">Số Lượt Đã Bán</th>
                    <th class="text-end">Doanh Thu Thu Về</th>
                    <th style="width: 250px;">Tỷ Lệ Đóng Góp (%)</th>
                    <th class="text-center pe-3">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topCoursesWithRevenue as $i => $item)
                    <tr>
                        <td class="ps-3 fw-bold text-muted">{{ $i + 1 }}</td>
                        <td>
                            <strong class="text-dark d-block fs-7">{{ $item->course_name }}</strong>
                            @if(isset($item->delivery_mode) && $item->delivery_mode === 'classroom')
                                <small class="text-primary"><i class="bi bi-building me-1"></i> Lớp học trực tiếp tại trung tâm</small>
                            @else
                                <small class="text-muted"><i class="bi bi-laptop text-primary me-1"></i> Khóa học trực tuyến LMS</small>
                            @endif
                        </td>
                        <td class="text-center font-monospace fs-7 text-dark fw-semibold">
                            {{ number_format($item->unit_price, 0, ',', '.') }} ₫
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary fs-7 px-3 py-2 rounded-pill fw-bold">
                                {{ $item->total_sold }} lượt bán
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fs-6 fw-bold text-success font-monospace">
                                {{ number_format($item->course_revenue, 0, ',', '.') }} ₫
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $item->percentage }}%;" aria-valuenow="{{ $item->percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="fs-8 fw-bold text-dark" style="min-width: 45px;">{{ $item->percentage }}%</span>
                            </div>
                        </td>
                        <td class="text-center pe-3">
                            <a href="{{ route('shop_admin.index') }}" class="btn btn-sm btn-light border" title="Xem danh sách khóa học">
                                <i class="bi bi-eye"></i> Xem khóa
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary"></i>
                            Chưa có dữ liệu giao dịch khóa học nào được ghi nhận.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($topCoursesWithRevenue->isNotEmpty())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="ps-3 text-uppercase text-secondary fs-8">TỔNG CỘNG THỰC THU KHÓA HỌC:</td>
                        <td class="text-center text-primary fs-7">{{ $topCoursesWithRevenue->sum('total_sold') }} lượt</td>
                        <td class="text-end text-success fs-6 font-monospace">{{ number_format($totalCourseRevenue, 0, ',', '.') }} ₫</td>
                        <td><span class="badge bg-success">100% Tổng doanh thu</span></td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ĐƠN HÀNG ĐẶT MUA GẦN ĐÂY CẦN ĐỐI SOÁT & VIETQR --}}
<div class="metis-card mb-0">
    <div class="metis-card-header bg-white d-flex align-items-center justify-content-between py-3">
        <div>
            <h5 class="metis-card-title d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-receipt text-primary fs-5"></i>
                <span class="fw-bold">Đơn Hàng Bán Khóa Học Gần Đây</span>
            </h5>
            <small class="text-secondary">Đối soát thanh toán chuyển khoản VietQR và trạng thái kích hoạt khóa học cho học viên.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('shop_admin.orders.index') }}" class="btn btn-sm btn-primary">
                Xem tất cả {{ $totalOrders }} đơn hàng →
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="fs-8 text-uppercase text-secondary">
                    <th class="ps-3 py-3" style="width: 220px; min-width: 200px;">Mã Đơn</th>
                    <th style="min-width: 180px;">Người Mua & Liên Hệ</th>
                    <th>Khóa Học Đặt Mua</th>
                    <th>Tổng Tiền</th>
                    <th>Hình Thức</th>
                    <th class="text-center">Trạng Thái</th>
                    <th class="text-end pe-3">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('shop_admin.orders.show', $order->number) }}" class="fw-bold text-primary font-monospace text-decoration-none">
                                {{ $order->number }}
                            </a>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="text-muted fs-8">{{ $order->created_at?->diffForHumans() }}</span>
                                @if($order->enrollment_id)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-9 px-1 py-0">Lớp trực tiếp</span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle fs-9 px-1 py-0">Online LMS</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong class="text-dark d-block fs-7">{{ $order->buyer_name }}</strong>
                            <small class="text-muted">{{ $order->buyer_email }}</small>
                        </td>
                        <td>
                            @if($order->items && $order->items->isNotEmpty())
                                @foreach($order->items as $item)
                                    <div class="fs-7 text-dark text-truncate" style="max-width: 320px;">
                                        <i class="bi bi-journal-check text-primary me-1"></i> {{ $item->course_name }}
                                    </div>
                                @endforeach
                            @else
                                <span class="text-muted fs-8">Gói học E-learning</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-dark fs-7 font-monospace">{{ number_format($order->total, 0, ',', '.') }} ₫</strong>
                        </td>
                        <td>
                            @if($order->payment && $order->payment->method === 'bank_transfer')
                                <span class="badge bg-info-subtle text-info border"><i class="bi bi-qr-code"></i> VietQR 24/7</span>
                            @else
                                <span class="badge bg-light text-secondary border">Mô phỏng</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($order->status === 'paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                    <i class="bi bi-check2-circle me-1"></i> Đã kích hoạt
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">
                                    <i class="bi bi-hourglass-split me-1"></i> Chờ duyệt VietQR
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('shop_admin.orders.show', $order->number) }}" class="btn btn-sm {{ $order->status === 'paid' ? 'btn-light border' : 'btn-warning text-dark fw-bold' }}">
                                {{ $order->status === 'paid' ? 'Chi tiết' : 'Duyệt đơn ngay' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Chưa có đơn hàng nào trong hệ thống.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
{{-- Nạp Chart.js từ file cục bộ public/js/chart.min.js hoặc fallback CDN --}}
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
    if (typeof Chart === 'undefined') {
        const cdnScript = document.createElement('script');
        cdnScript.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
        document.head.appendChild(cdnScript);
        cdnScript.onload = initCharts;
    } else {
        initCharts();
    }

    function initCharts() {
        const chartDates = @json($chartDates);
        const chartDailyRevenues = @json($chartDailyRevenues);
        const chartDailyOrders = @json($chartDailyOrders);
        const chartCourseLabels = @json($chartCourseLabels);
        const chartCourseRevenues = @json($chartCourseRevenues);

        // 1. BIỂU ĐỒ XU HƯỚNG DOANH THU THEO NGÀY (LINE/AREA CHART)
        const revenueCtx = document.getElementById('revenueTrendChart')?.getContext('2d');
        if (revenueCtx) {
            let currentMode = 'revenue';
            
            // Gradient màu xanh ngọc
            const gradientRevenue = revenueCtx.createLinearGradient(0, 0, 0, 300);
            gradientRevenue.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            gradientRevenue.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            const gradientOrders = revenueCtx.createLinearGradient(0, 0, 0, 300);
            gradientOrders.addColorStop(0, 'rgba(37, 99, 235, 0.4)');
            gradientOrders.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

            const trendChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: chartDates,
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: chartDailyRevenues,
                        borderColor: '#10b981',
                        backgroundColor: gradientRevenue,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    if (currentMode === 'revenue') {
                                        return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' ₫';
                                    } else {
                                        return 'Số đơn hàng: ' + context.raw + ' đơn';
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: {
                                callback: function(value) {
                                    if (currentMode === 'revenue') {
                                        if (value >= 1000000) return (value / 1000000) + ' tr ₫';
                                        if (value >= 1000) return (value / 1000) + ' k ₫';
                                        return value + ' ₫';
                                    }
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Toggle Doanh thu / Số đơn
            const btnRevenue = document.getElementById('btnChartRevenue');
            const btnOrders = document.getElementById('btnChartOrders');

            btnRevenue?.addEventListener('click', function() {
                currentMode = 'revenue';
                btnRevenue.classList.add('active');
                btnOrders.classList.remove('active');
                trendChart.data.datasets[0].label = 'Doanh thu (₫)';
                trendChart.data.datasets[0].data = chartDailyRevenues;
                trendChart.data.datasets[0].borderColor = '#10b981';
                trendChart.data.datasets[0].backgroundColor = gradientRevenue;
                trendChart.data.datasets[0].pointBackgroundColor = '#10b981';
                trendChart.update();
            });

            btnOrders?.addEventListener('click', function() {
                currentMode = 'orders';
                btnOrders.classList.add('active');
                btnRevenue.classList.remove('active');
                trendChart.data.datasets[0].label = 'Số đơn hàng';
                trendChart.data.datasets[0].data = chartDailyOrders;
                trendChart.data.datasets[0].borderColor = '#2563eb';
                trendChart.data.datasets[0].backgroundColor = gradientOrders;
                trendChart.data.datasets[0].pointBackgroundColor = '#2563eb';
                trendChart.update();
            });
        }

        // 2. BIỂU ĐỒ TRÒN TỶ TRỌNG DOANH THU THEO KHÓA HỌC (DONUT CHART)
        const courseCtx = document.getElementById('courseShareChart')?.getContext('2d');
        if (courseCtx && chartCourseLabels.length > 0) {
            new Chart(courseCtx, {
                type: 'doughnut',
                data: {
                    labels: chartCourseLabels,
                    datasets: [{
                        data: chartCourseRevenues,
                        backgroundColor: [
                            '#10b981', // Emerald
                            '#3b82f6', // Blue
                            '#8b5cf6', // Purple
                            '#f59e0b', // Amber
                            '#ec4899', // Pink
                            '#9ca3af'  // Gray
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + new Intl.NumberFormat('vi-VN').format(value) + ' ₫ (' + percent + '%)';
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    }
</script>
@endsection

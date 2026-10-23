@extends('layouts.app')

@section('title', 'Tổng quan thống kê')

@section('content')
@php
    $chartRows = $courses->flatMap(fn ($course) => $course->classRooms->map(fn ($classRoom) => [
        'code' => $classRoom->code,
        'name' => $classRoom->name,
        'revenue' => (float) ($classRoom->paid_revenue ?? 0),
    ]));
    $maxRevenue = max(1, (float) $chartRows->max('revenue'));
@endphp

<div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <div class="page-eyebrow mb-2">Báo cáo trung tâm</div>
        <h1 class="h2 fw-bold mb-2">Tổng quan hoạt động</h1>
        <p class="text-secondary mb-0">Theo dõi quy mô đào tạo và học phí đã thu theo từng lớp.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('reports.export') }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i> Xuất Excel Báo Cáo</a>
        <span class="report-live"><i class="bi bi-arrow-repeat me-1"></i> Dữ liệu trực tiếp</span>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Học viên', 'value' => number_format($summary['students']), 'icon' => 'bi-people', 'tone' => 'blue', 'hint' => 'Hồ sơ học viên'],
        ['label' => 'Giảng viên', 'value' => number_format($summary['teachers']), 'icon' => 'bi-person-video3', 'tone' => 'green', 'hint' => 'Đội ngũ giảng dạy'],
        ['label' => 'Lớp học', 'value' => number_format($summary['classes']), 'icon' => 'bi-journal-bookmark', 'tone' => 'amber', 'hint' => 'Tất cả trạng thái'],
        ['label' => 'Học phí đã thu', 'value' => number_format((float) $summary['revenue'], 0, ',', '.').' ₫', 'icon' => 'bi-cash-stack', 'tone' => 'violet', 'hint' => 'Tổng khoản đã thanh toán'],
    ] as $card)
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><div class="stat-label">{{ $card['label'] }}</div><div class="stat-value">{{ $card['value'] }}</div><div class="stat-hint">{{ $card['hint'] }}</div></div>
                    <span class="stat-icon stat-{{ $card['tone'] }}"><i class="bi {{ $card['icon'] }}"></i></span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="status-card"><span class="status-dot dot-blue"></span><div><div class="status-number">{{ $summary['ongoing'] }}</div><div class="status-label">Lớp đang học</div></div><i class="bi bi-play-circle status-watermark"></i></div></div>
    <div class="col-md-4"><div class="status-card"><span class="status-dot dot-amber"></span><div><div class="status-number">{{ $summary['upcoming'] }}</div><div class="status-label">Lớp sắp mở</div></div><i class="bi bi-calendar-event status-watermark"></i></div></div>
    <div class="col-md-4"><div class="status-card"><span class="status-dot dot-slate"></span><div><div class="status-number">{{ $summary['completed'] }}</div><div class="status-label">Lớp đã hoàn thành</div></div><i class="bi bi-check-circle status-watermark"></i></div></div>
</div>

<section class="card report-card mb-4">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4">
        <div><h2 class="h5 fw-bold mb-1">Học phí đã thu theo lớp</h2><p class="text-secondary small mb-0">So sánh tổng khoản thanh toán đã ghi nhận ở từng lớp.</p></div>
        <span class="chart-unit"><i class="bi bi-bar-chart-fill me-1"></i> Đơn vị: VNĐ</span>
    </div>
    @if($chartRows->isNotEmpty())
        <div class="chart-scroll">
            <div class="revenue-chart" role="img" aria-label="Biểu đồ cột học phí đã thu theo lớp">
                <div class="chart-y-labels"><span>{{ number_format($maxRevenue, 0, ',', '.') }}</span><span>{{ number_format($maxRevenue * .75, 0, ',', '.') }}</span><span>{{ number_format($maxRevenue * .5, 0, ',', '.') }}</span><span>{{ number_format($maxRevenue * .25, 0, ',', '.') }}</span><span>0</span></div>
                <div class="chart-content">
                    <div class="chart-grid"></div>
                    <div class="chart-columns">
                        @foreach($chartRows as $row)
                            @php $barHeight = $row['revenue'] > 0 ? max(4, ($row['revenue'] / $maxRevenue) * 100) : 0; @endphp
                            <div class="chart-column" title="{{ $row['name'] }}: {{ number_format($row['revenue'], 0, ',', '.') }} ₫">
                                <div class="chart-bar-area">
                                    @if($row['revenue'] > 0)<span class="chart-value" style="bottom: calc({{ $barHeight }}% + 6px)">{{ number_format($row['revenue'], 0, ',', '.') }}</span>@endif
                                    <div class="chart-bar" style="height: {{ $barHeight }}%"></div>
                                </div>
                                <span class="chart-class-code">{{ $row['code'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="chart-empty"><i class="bi bi-bar-chart-line"></i><span>Chưa có lớp để hiển thị biểu đồ.</span></div>
    @endif
</section>

<section class="card report-card">
    <div class="card-header bg-white py-3 px-4"><h2 class="h5 fw-bold mb-1">Chi tiết khóa học và lớp học</h2><p class="text-secondary small mb-0">Sĩ số hiện tại và số học phí đã thu ở từng lớp.</p></div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>Khóa học / Lớp</th><th>Trạng thái</th><th class="text-end">Sĩ số</th><th class="text-end">Học phí đã thu</th></tr></thead>
        <tbody>
        @forelse($courses as $course)
            <tr class="course-group-row"><td class="fw-bold"><i class="bi bi-book-half me-2"></i>{{ $course->code }} · {{ $course->name }}</td><td colspan="2" class="text-secondary">{{ $course->classRooms->count() }} lớp</td><td></td></tr>
            @forelse($course->classRooms as $classRoom)
                <tr><td class="ps-4"><a class="report-class-link" href="{{ route('class_rooms.show', $classRoom) }}"><span class="class-link-dot"></span>{{ $classRoom->code }} · {{ $classRoom->name }}</a></td><td><span class="badge text-bg-{{ $classRoom->status === 'ongoing' ? 'primary' : ($classRoom->status === 'upcoming' ? 'warning' : 'secondary') }}">{{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? $classRoom->status }}</span></td><td class="text-end">{{ $classRoom->enrollments_count }} / {{ $classRoom->max_students }}</td><td class="text-end fw-semibold">{{ number_format((float) ($classRoom->paid_revenue ?? 0), 0, ',', '.') }} ₫</td></tr>
            @empty<tr><td colspan="4" class="ps-4 text-secondary">Chưa có lớp thuộc khóa học này.</td></tr>@endforelse
        @empty
            <tr><td colspan="4" class="text-center py-5 text-secondary">Chưa có dữ liệu khóa học.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</section>

<style>
    .report-live,.chart-unit { display:inline-flex; align-items:center; border:1px solid #dce5fa; border-radius:999px; padding:.5rem .8rem; color:#3457d5; background:#f4f7ff; font-size:.8rem; font-weight:650; }
    .stat-card .card-body { padding:1.25rem; }
    .stat-label,.status-label { color:#6c7b91; font-size:.84rem; font-weight:600; }
    .stat-value { margin-top:.38rem; color:#192b4c; font-size:clamp(1.45rem,2vw,1.9rem); font-weight:750; letter-spacing:-.04em; }
    .stat-hint { margin-top:.28rem; color:#94a0b2; font-size:.76rem; }
    .stat-icon { display:grid; width:48px; height:48px; place-items:center; border-radius:15px; font-size:1.3rem; }
    .stat-blue { color:#3457d5; background:#edf1ff; } .stat-green { color:#13835f; background:#e6f7f0; } .stat-amber { color:#bd7a12; background:#fff4df; } .stat-violet { color:#7651c7; background:#f1ebff; }
    .status-card { position:relative; display:flex; align-items:center; gap:.85rem; min-height:92px; padding:1rem 1.2rem; overflow:hidden; border:1px solid #e2e8f1; border-radius:16px; background:#fff; box-shadow:0 5px 18px rgba(26,45,85,.035); }
    .status-dot { width:10px; height:10px; border-radius:50%; } .dot-blue { background:#4a68da; box-shadow:0 0 0 5px #eef1ff; } .dot-amber { background:#e7a73a; box-shadow:0 0 0 5px #fff5e4; } .dot-slate { background:#8290a5; box-shadow:0 0 0 5px #f0f2f6; }
    .status-number { color:#1b2d4c; font-size:1.3rem; font-weight:750; line-height:1.2; } .status-watermark { position:absolute; right:1.2rem; color:#edf0f6; font-size:2rem; }
    .report-card .card-header { border-bottom:1px solid #edf0f5; }
    .chart-scroll { overflow-x:auto; padding:1.35rem 1.5rem 1.6rem; }
    .revenue-chart { display:flex; min-width:max(100%, 560px); height:300px; }
    .chart-y-labels { display:flex; flex:0 0 82px; flex-direction:column; justify-content:space-between; padding:0 12px 33px 0; color:#8995a8; font-size:.68rem; text-align:right; }
    .chart-content { position:relative; flex:1; min-width:0; height:267px; }
    .chart-grid { position:absolute; inset:25px 0 33px; background:repeating-linear-gradient(to bottom,transparent 0,transparent calc(25% - 1px),#e9edf4 calc(25% - 1px),#e9edf4 25%); border-bottom:1px solid #dfe5ef; }
    .chart-columns { position:absolute; inset:25px 0 33px; display:flex; align-items:stretch; gap:clamp(12px,2.6vw,32px); overflow-x:auto; padding:0 12px; }
    .chart-column { display:flex; flex:1 0 70px; flex-direction:column; align-items:center; justify-content:flex-end; min-width:70px; max-width:150px; height:100%; }
    .chart-bar-area { position:relative; display:flex; align-items:flex-end; justify-content:center; width:100%; height:calc(100% - 33px); }
    .chart-bar { width:min(54px,58%); min-height:0; border-radius:9px 9px 3px 3px; background:linear-gradient(180deg,#7188ef 0%,#4564db 100%); box-shadow:0 5px 13px rgba(69,100,219,.19); transition:filter .2s,transform .2s; }
    .chart-column:hover .chart-bar { filter:saturate(1.2); transform:scaleX(1.06); }
    .chart-value { position:absolute; bottom:calc(min(100%, var(--bar-height, 0%)) + 6px); top:auto; color:#5c6d88; font-size:.68rem; white-space:nowrap; }
    .chart-class-code { width:100%; overflow:hidden; padding-top:9px; color:#687890; font-size:.7rem; text-align:center; text-overflow:ellipsis; white-space:nowrap; }
    .chart-empty { display:flex; min-height:220px; flex-direction:column; align-items:center; justify-content:center; gap:.6rem; color:#8b98ac; }
    .chart-empty i { color:#c5cede; font-size:2.3rem; }
    .course-group-row td { background:#f4f7fd; color:#314769; }
    .report-class-link { display:inline-flex; align-items:center; gap:.6rem; color:#3457d5; text-decoration:none; }
    .report-class-link:hover { color:#263fa8; text-decoration:underline; }
    .class-link-dot { width:7px; height:7px; border-radius:50%; background:#8195e9; }
    @media(max-width:575px) { .chart-scroll { padding-right:1rem; padding-left:1rem; } .chart-y-labels { flex-basis:65px; } }
</style>
@endsection

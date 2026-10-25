@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Quản lý hóa đơn')

@section('content')
<section data-live-search data-item-label="hóa đơn" data-login-url="{{ route('login') }}">
<div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-3">
    <div>
        <div class="page-eyebrow mb-2">Tài chính trung tâm</div>
        <h1 class="h2 fw-bold mb-2">Quản lý hóa đơn</h1>
        <p class="text-secondary mb-0">Theo dõi hóa đơn học phí, học viên, giáo viên phụ trách và lớp học tương ứng.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="invoice-pill"><strong>{{ number_format($summary['total']) }}</strong> hóa đơn</span>
        <span class="invoice-pill invoice-pill-paid"><strong>{{ number_format($summary['paid']) }}</strong> đã thanh toán</span>
        <span class="invoice-pill invoice-pill-unpaid"><strong>{{ number_format($summary['unpaid']) }}</strong> chưa thanh toán</span>
    </div>
</div>

<form method="GET" action="{{ route('invoices.index') }}" data-search-form class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-3">
                <label for="keyword" class="form-label">Tìm kiếm nhanh</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" id="keyword" name="keyword" class="form-control" value="{{ $keyword }}" maxlength="100" autocomplete="off" placeholder="Mã hóa đơn, học viên, giáo viên, lớp học...">
                </div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label for="status" class="form-label">Thanh toán</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="paid" @selected($status === 'paid')>Đã thanh toán</option>
                    <option value="unpaid" @selected($status === 'unpaid')>Chưa thanh toán</option>
                </select>
            </div>
            <div class="col-4 col-lg-1">
                <label for="day" class="form-label">Ngày</label>
                <select id="day" name="day" class="form-select">
                    <option value="">Tất cả</option>
                    @foreach(range(1, 31) as $day)
                        <option value="{{ $day }}" @selected((string) request('day') === (string) $day)>{{ $day }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 col-lg-1">
                <label for="month" class="form-label">Tháng</label>
                <select id="month" name="month" class="form-select">
                    <option value="">Tất cả</option>
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" @selected((string) request('month') === (string) $month)>{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 col-lg-1">
                <label for="year" class="form-label">Năm</label>
                <select id="year" name="year" class="form-select">
                    <option value="">Tất cả</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label for="sort" class="form-label">Sắp xếp</label>
                <select id="sort" name="sort" class="form-select">
                    <option value="">Mặc định</option>
                    <option value="newest" @selected($sort === 'newest')>Mới nhất trước</option>
                    <option value="oldest" @selected($sort === 'oldest')>Cũ nhất trước</option>
                    <option value="total_desc" @selected($sort === 'total_desc')>Tổng tiền giảm dần</option>
                    <option value="total_asc" @selected($sort === 'total_asc')>Tổng tiền tăng dần</option>
                </select>
            </div>
            <div class="col-12 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i> Lọc</button>
                <a href="{{ route('invoices.index') }}" data-search-reset class="btn btn-outline-secondary" title="Xóa bộ lọc" aria-label="Xóa bộ lọc"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </div>
    </div>
</form>

<div class="d-flex align-items-center gap-2 mb-3">
    <span data-search-loading hidden aria-hidden="true"><span class="spinner-border spinner-border-sm text-primary"></span></span>
    <p class="small text-secondary mb-0" data-search-status role="status" aria-live="polite" aria-atomic="true">Danh sách tự động cập nhật khi bạn tìm kiếm hoặc đổi bộ lọc.</p>
</div>
<div class="alert alert-danger" data-search-error hidden role="alert">
    <span data-search-error-message></span>
    <button type="button" class="btn btn-sm btn-outline-danger ms-2" data-search-retry>Thử lại</button>
</div>

<div id="invoice-results" data-search-results aria-busy="false" tabindex="-1">
    @include('invoices._results')
</div>
</section>

<script src="{{ asset('js/member-one-search.js') }}" defer></script>

<style>
    .invoice-pill { display:inline-flex; align-items:center; gap:.35rem; border:1px solid #dce5fa; border-radius:999px; padding:.5rem .8rem; color:#4c5e7d; background:#f8faff; font-size:.8rem; }
    .invoice-pill strong { color:#1d2e4d; }
    .invoice-pill-paid { border-color:#cdeee1; background:#f1fbf7; color:#17795a; }
    .invoice-pill-unpaid { border-color:#f6e3bf; background:#fffaf1; color:#9b6510; }
    .invoice-pill-paid strong { color:#17795a; }
    .invoice-pill-unpaid strong { color:#9b6510; }
    .invoice-code { color:#3457d5; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:.86rem; font-weight:700; text-decoration:none; white-space:nowrap; }
    .invoice-code:hover { color:#263fa8; text-decoration:underline; }
    .invoice-total-label { display:inline-flex; align-items:center; border:1px solid #dce5fa; border-radius:999px; padding:.5rem .8rem; color:#3457d5; background:#f4f7ff; font-size:.8rem; font-weight:650; }
</style>
@endsection

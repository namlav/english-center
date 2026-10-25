@extends('layouts.admin')

@section('title', 'Quản lý Khóa học')

@section('content')
<section data-live-search data-item-label="khóa học" data-login-url="{{ route('login') }}">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold mb-0"><i class="bi bi-book-half"></i> Quản lý Khóa học</h3>
    @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('courses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm khóa học
        </a>
    @endif
</div>

<form method="GET" action="{{ route('courses.index') }}" data-search-form class="card shadow-sm mb-4">
    <div class="card-body row g-3 align-items-end">
        <div class="col-md-7"><label for="keyword" class="form-label">Tìm mã hoặc tên khóa học</label><input id="keyword" name="keyword" type="search" autocomplete="off" maxlength="100" class="form-control" value="{{ $keyword }}"></div>
        <div class="col-md-3"><label for="status" class="form-label">Trạng thái</label><select id="status" name="status" class="form-select"><option value="">Tất cả</option><option value="1" @selected($status === '1')>Hoạt động</option><option value="0" @selected($status === '0')>Ngừng hoạt động</option></select></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary" type="submit">Lọc</button><a class="btn btn-outline-secondary" data-search-reset href="{{ route('courses.index') }}">Đặt lại</a></div>
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
<div id="courses-results" data-search-results aria-busy="false" tabindex="-1">
    @include('courses._results')
</div>
</section>
<script src="{{ asset('js/member-one-search.js') }}" defer></script>
@endsection

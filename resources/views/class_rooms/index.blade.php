@extends('layouts.app')

@section('title', 'Danh Sách Lớp Học - Tra Cứu')

@section('content')
<section data-live-search data-item-label="lớp học" data-login-url="{{ route('login') }}">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold mb-0"><i class="bi bi-calendar3"></i> Danh sách Lớp học</h3>
    @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('class_rooms.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm Lớp học mới
        </a>
    @endif
</div>

<!-- Bộ lọc & Tìm kiếm -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('class_rooms.index') }}" data-search-form class="row g-3">
            <div class="col-md-4">
                <label for="class-keyword" class="form-label fw-semibold">Từ khóa (Mã/Tên lớp)</label>
                <input id="class-keyword" type="search" name="keyword" maxlength="100" autocomplete="off" class="form-control" placeholder="Nhập mã hoặc tên lớp..." value="{{ request('keyword') }}">
            </div>
            <div class="col-md-3">
                <label for="class-course" class="form-label fw-semibold">Khóa học</label>
                <select id="class-course" name="course_id" class="form-select">
                    <option value="">-- Tất cả khóa học --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->code }} - {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="class-teacher" class="form-label fw-semibold">Giáo viên</label>
                <select id="class-teacher" name="teacher_id" class="form-select">
                    <option value="">-- Tất cả giáo viên --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->teacher_code }} - {{ $teacher->user->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Lọc</button>
                <a data-search-reset href="{{ route('class_rooms.index') }}" class="btn btn-outline-secondary" aria-label="Đặt lại bộ lọc"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
            <div class="col-md-3"><label for="class-status" class="form-label fw-semibold">Trạng thái lớp</label><select id="class-status" name="status" class="form-select"><option value="">Tất cả trạng thái</option>@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
        </form>
    </div>
</div>

<!-- Bảng hiển thị danh sách lớp -->

<div class="d-flex align-items-center gap-2 mb-3">
    <span data-search-loading hidden aria-hidden="true"><span class="spinner-border spinner-border-sm text-primary"></span></span>
    <p class="small text-secondary mb-0" data-search-status role="status" aria-live="polite" aria-atomic="true">Danh sách tự động cập nhật khi bạn tìm kiếm hoặc đổi bộ lọc.</p>
</div>
<div class="alert alert-danger" data-search-error hidden role="alert">
    <span data-search-error-message></span>
    <button type="button" class="btn btn-sm btn-outline-danger ms-2" data-search-retry>Thử lại</button>
</div>
<div id="class_rooms-results" data-search-results aria-busy="false" tabindex="-1">
    @include('class_rooms._results')
</div>
</section>
<script src="{{ asset('js/member-one-search.js') }}" defer></script>
@endsection

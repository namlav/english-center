@extends('layouts.app')

@section('title', 'Chi tiết lớp học')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div><span class="badge bg-primary-subtle text-primary mb-2">{{ $classRoom->code }}</span><h2 class="fw-bold mb-1">{{ $classRoom->name }}</h2><span class="badge bg-secondary">{{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? $classRoom->status }}</span></div>
    <div class="d-flex gap-2"><a href="{{ route('class_rooms.index') }}" class="btn btn-outline-secondary">Về danh sách</a>@if(auth()->user()->isAdmin())<a href="{{ route('class_rooms.edit', $classRoom) }}" class="btn btn-primary">Sửa lớp</a>@endif</div>
</div>
<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-secondary mb-2">Học phí</p><strong class="fs-3 text-primary">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} đ</strong></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-secondary mb-2">Sĩ số / Tối đa</p><strong class="fs-3">{{ $classRoom->enrollments_count }} / {{ $classRoom->max_students }}</strong></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-secondary mb-2">Ngày bắt đầu</p><strong class="fs-3">{{ $classRoom->start_date->format('d/m/Y') }}</strong></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><p class="text-secondary mb-2">Ngày kết thúc dự kiến</p><strong class="fs-3">{{ $classRoom->expected_end_date?->format('d/m/Y') ?? 'Chưa cập nhật' }}</strong></div></div></div>
</div>
<div class="row g-4">
    <div class="col-md-7"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><h3 class="h5 mb-3">Lịch học</h3><p class="mb-0" style="white-space:pre-wrap">{{ $classRoom->schedule }}</p></div></div></div>
    <div class="col-md-5"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><h3 class="h5 mb-3">Thông tin đào tạo</h3><dl class="mb-0"><dt class="text-secondary fw-normal">Khóa học</dt><dd>@if(auth()->user()->isAdmin())<a href="{{ route('courses.show', $classRoom->course) }}">{{ $classRoom->course->name }}</a>@else{{ $classRoom->course->name }}@endif</dd><dt class="text-secondary fw-normal">Giảng viên phụ trách</dt><dd>{{ $classRoom->teacher?->user?->name ?? 'Chưa cập nhật' }}</dd><dt class="text-secondary fw-normal">Mã giảng viên</dt><dd class="mb-0">{{ $classRoom->teacher?->teacher_code }}</dd></dl></div></div></div>
</div>

@endsection

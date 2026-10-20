@extends('layouts.app')

@section('title', 'Thông tin lớp học')

@section('content')

@php($isEdit = $classRoom->exists)
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h2 class="fw-bold mb-1">{{ $isEdit ? 'Sửa lớp học' : 'Thêm lớp học' }}</h2><p class="text-secondary mb-0">Phân công giảng viên và tổ chức lớp học.</p></div><a class="btn btn-outline-secondary" href="{{ route('class_rooms.index') }}">Về danh sách</a></div>
@if($courses->isEmpty() || $teachers->isEmpty())
    <div class="alert alert-warning">Cần có ít nhất một khóa học hoạt động và một giảng viên hoạt động trước khi mở lớp.</div>
@else
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <p class="text-secondary">Các trường có dấu <span class="text-danger">*</span> là bắt buộc. Học phí được thiết lập cho từng lớp.</p>
    <form method="POST" action="{{ $isEdit ? route('class_rooms.update', $classRoom) : route('class_rooms.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <div class="row g-4">
            <div class="col-md-4"><label for="code" class="form-label">Mã lớp *</label><input id="code" name="code" class="form-control" maxlength="30" value="{{ old('code', $classRoom->code) }}" required></div>
            <div class="col-md-8"><label for="name" class="form-label">Tên lớp *</label><input id="name" name="name" class="form-control" maxlength="255" value="{{ old('name', $classRoom->name) }}" required></div>
            <div class="col-md-6"><label for="course_id" class="form-label">Khóa học *</label><select id="course_id" name="course_id" class="form-select" required><option value="">Chọn khóa học</option>@foreach($courses as $course)<option value="{{ $course->id }}" @selected((string) old('course_id', $classRoom->course_id) === (string) $course->id)>{{ $course->code }} — {{ $course->name }}{{ $course->is_active ? '' : ' (Ngừng hoạt động)' }}</option>@endforeach</select></div>
            <div class="col-md-6"><label for="teacher_id" class="form-label">Giảng viên *</label><select id="teacher_id" name="teacher_id" class="form-select" required><option value="">Chọn giảng viên</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected((string) old('teacher_id', $classRoom->teacher_id) === (string) $teacher->id)>{{ $teacher->teacher_code }} — {{ $teacher->user?->name }}{{ $teacher->user?->is_active ? '' : ' (Đã khóa)' }}</option>@endforeach</select></div>
            <div class="col-md-4"><label for="start_date" class="form-label">Ngày bắt đầu *</label><input id="start_date" name="start_date" type="date" class="form-control" value="{{ old('start_date', $classRoom->start_date?->format('Y-m-d')) }}" required></div>
            <div class="col-md-4"><label for="expected_end_date" class="form-label">Ngày kết thúc dự kiến *</label><input id="expected_end_date" name="expected_end_date" type="date" min="{{ old('start_date', $classRoom->start_date?->format('Y-m-d')) }}" class="form-control" value="{{ old('expected_end_date', $classRoom->expected_end_date?->format('Y-m-d')) }}" required></div>
            <div class="col-md-4"><label for="tuition_fee" class="form-label">Học phí (đồng) *</label><input id="tuition_fee" name="tuition_fee" type="number" min="0" max="9999999999.99" step="0.01" class="form-control" value="{{ old('tuition_fee', $classRoom->tuition_fee) }}" required></div>
            <div class="col-md-4"><label for="max_students" class="form-label">Sĩ số tối đa *</label><input id="max_students" name="max_students" type="number" min="1" max="65535" class="form-control" value="{{ old('max_students', $classRoom->max_students) }}" required></div>
            <div class="col-md-8"><label for="schedule" class="form-label">Lịch học *</label><input id="schedule" name="schedule" class="form-control" maxlength="255" placeholder="Thứ 2, 4, 6 — 18:00–19:30" value="{{ old('schedule', $classRoom->schedule) }}" required></div>
            <div class="col-md-4"><label for="status" class="form-label">Trạng thái *</label><select id="status" name="status" class="form-select">@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected(old('status', $classRoom->status) === $value)>{{ $label }}</option>@endforeach</select></div>
        </div>
        <div class="border-top pt-4 mt-4 d-flex gap-2"><button class="btn btn-primary" type="submit">{{ $isEdit ? 'Lưu thay đổi' : 'Tạo lớp học' }}</button><a class="btn btn-outline-secondary" href="{{ route('class_rooms.index') }}">Hủy</a></div>
    </form>
</div></div>
@endif

@endsection

@extends('layouts.app')

@section('title', 'Thông tin khóa học')

@section('content')

@php($isEdit = $course->exists)
<div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4">
    <div><h2 class="fw-bold mb-1">{{ $isEdit ? 'Sửa khóa học' : 'Thêm khóa học' }}</h2><p class="text-secondary mb-0">Thông tin chương trình đào tạo của trung tâm.</p></div>
    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Về danh sách</a>
</div>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <p class="text-secondary">Các trường có dấu <span class="text-danger">*</span> là bắt buộc.</p>
    <form method="POST" action="{{ $isEdit ? route('courses.update', $course) : route('courses.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <div class="row g-4">
            <div class="col-md-4"><label for="code" class="form-label">Mã khóa học *</label><input id="code" name="code" class="form-control" maxlength="30" value="{{ old('code', $course->code) }}" required></div>
            <div class="col-md-8"><label for="name" class="form-label">Tên khóa học *</label><input id="name" name="name" class="form-control" maxlength="255" value="{{ old('name', $course->name) }}" required></div>
            <div class="col-md-4"><label for="level" class="form-label">Trình độ</label><input id="level" name="level" class="form-control" maxlength="255" value="{{ old('level', $course->level) }}"></div>
            <div class="col-md-4"><label for="duration_hours" class="form-label">Thời lượng (giờ) *</label><input id="duration_hours" name="duration_hours" type="number" min="1" max="2147483647" class="form-control" value="{{ old('duration_hours', $course->duration_hours) }}" required></div>
            <div class="col-md-4"><label for="is_active" class="form-label">Trạng thái *</label><select id="is_active" name="is_active" class="form-select"><option value="1" @selected((string) old('is_active', (int) $course->is_active) === '1')>Hoạt động</option><option value="0" @selected((string) old('is_active', (int) $course->is_active) === '0')>Ngừng hoạt động</option></select></div>
            <div class="col-12"><label for="description" class="form-label">Mô tả chương trình</label><textarea id="description" name="description" class="form-control" rows="5" maxlength="5000">{{ old('description', $course->description) }}</textarea></div>
        </div>
        <div class="border-top pt-4 mt-4 d-flex gap-2"><button class="btn btn-primary" type="submit">{{ $isEdit ? 'Lưu thay đổi' : 'Tạo khóa học' }}</button><a class="btn btn-outline-secondary" href="{{ route('courses.index') }}">Hủy</a></div>
    </form>
</div></div>

@endsection

@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', $teacher->exists ? 'Sửa giảng viên' : 'Thêm giảng viên')

@section('content')

@php($isEdit = $teacher->exists)
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ $isEdit ? 'Sửa giảng viên' : 'Thêm giảng viên' }}</h2>
        <p class="text-secondary mb-0">Thông tin tài khoản và hồ sơ giảng viên.</p>
    </div>
    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Về danh sách</a>
</div>

<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <p class="text-secondary">Các trường có dấu <span class="text-danger">*</span> là bắt buộc.</p>
    <form method="POST" action="{{ $isEdit ? route('teachers.update', $teacher) : route('teachers.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <div class="row g-4">
            <div class="col-md-6">
                <label for="name" class="form-label">Họ và tên *</label>
                <input id="name" name="name" class="form-control" maxlength="255" value="{{ old('name', $teacher->user?->name) }}" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email *</label>
                <input id="email" name="email" type="email" class="form-control" maxlength="255" value="{{ old('email', $teacher->user?->email) }}" required>
            </div>
            @unless($isEdit)
                <div class="col-md-6">
                    <label for="password" class="form-label">Mật khẩu *</label>
                    <input id="password" name="password" type="password" class="form-control" minlength="6" required>
                </div>
            @endunless
            <div class="col-md-6">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input id="phone" name="phone" class="form-control" maxlength="20" value="{{ old('phone', $teacher->user?->phone) }}">
            </div>
            <div class="col-md-4">
                <label for="teacher_code" class="form-label">Mã giảng viên *</label>
                <input id="teacher_code" name="teacher_code" class="form-control" maxlength="30" value="{{ old('teacher_code', $teacher->teacher_code) }}" required>
            </div>
            <div class="col-md-4">
                <label for="specialization" class="form-label">Chuyên môn *</label>
                <input id="specialization" name="specialization" class="form-control" maxlength="255" value="{{ old('specialization', $teacher->specialization) }}" required>
            </div>
            <div class="col-md-4">
                <label for="degree" class="form-label">Bằng cấp</label>
                <input id="degree" name="degree" class="form-control" maxlength="255" value="{{ old('degree', $teacher->degree) }}">
            </div>
            <div class="col-12">
                <label for="bio" class="form-label">Giới thiệu</label>
                <textarea id="bio" name="bio" class="form-control" rows="5">{{ old('bio', $teacher->bio) }}</textarea>
            </div>
        </div>
        <div class="border-top pt-4 mt-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Lưu thay đổi' : 'Thêm giảng viên' }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('teachers.index') }}">Hủy</a>
        </div>
    </form>
</div></div>

@endsection

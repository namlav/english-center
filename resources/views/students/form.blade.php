@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', $student->exists ? 'Sửa học viên' : 'Thêm học viên')

@section('content')

@php($isEdit = $student->exists)
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ $isEdit ? 'Sửa học viên' : 'Thêm học viên' }}</h2>
        <p class="text-secondary mb-0">Thông tin tài khoản và hồ sơ học viên.</p>
    </div>
    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Về danh sách</a>
</div>

<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <p class="text-secondary">Các trường có dấu <span class="text-danger">*</span> là bắt buộc.</p>
    <form method="POST" action="{{ $isEdit ? route('students.update', $student) : route('students.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <div class="row g-4">
            <div class="col-md-6">
                <label for="name" class="form-label">Họ và tên *</label>
                <input id="name" name="name" class="form-control" maxlength="255" value="{{ old('name', $student->user?->name) }}" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email *</label>
                <input id="email" name="email" type="email" class="form-control" maxlength="255" value="{{ old('email', $student->user?->email) }}" required>
            </div>
            @unless($isEdit)
                <div class="col-md-6">
                    <label for="password" class="form-label">Mật khẩu *</label>
                    <input id="password" name="password" type="password" class="form-control" minlength="6" required>
                </div>
            @endunless
            <div class="col-md-6">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input id="phone" name="phone" class="form-control" maxlength="20" value="{{ old('phone', $student->user?->phone) }}">
            </div>
            <div class="col-md-6">
                <label for="student_code" class="form-label">Mã học viên *</label>
                <input id="student_code" name="student_code" class="form-control" maxlength="30" value="{{ old('student_code', $student->student_code) }}" required>
            </div>
            <div class="col-md-6">
                <label for="date_of_birth" class="form-label">Ngày sinh</label>
                <input id="date_of_birth" name="date_of_birth" type="date" class="form-control" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-6">
                <label for="gender" class="form-label">Giới tính</label>
                <select id="gender" name="gender" class="form-select">
                    <option value="">Chọn giới tính</option>
                    <option value="male" @selected(old('gender', $student->gender) === 'male')>Nam</option>
                    <option value="female" @selected(old('gender', $student->gender) === 'female')>Nữ</option>
                    <option value="other" @selected(old('gender', $student->gender) === 'other')>Khác</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="address" class="form-label">Địa chỉ</label>
                <input id="address" name="address" class="form-control" maxlength="255" value="{{ old('address', $student->address) }}">
            </div>
        </div>
        <div class="border-top pt-4 mt-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Lưu thay đổi' : 'Thêm học viên' }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('students.index') }}">Hủy</a>
        </div>
    </form>
</div></div>

@endsection

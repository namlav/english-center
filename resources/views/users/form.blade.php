@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', $user->exists ? 'Sửa tài khoản' : 'Thêm tài khoản')

@section('content')

@php($isEdit = $user->exists)
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">{{ $isEdit ? 'Sửa tài khoản' : 'Thêm tài khoản' }}</h2>
        <p class="text-secondary mb-0">Thông tin đăng nhập và quyền của tài khoản.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Về danh sách</a>
</div>

<div class="card border-0 shadow-sm"><div class="card-body p-4">
    @if($isEdit && $user->isAdmin())
        <div class="alert alert-primary border-0 shadow-sm d-flex align-items-center gap-3 mb-4 py-3 px-3 rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe !important;">
            <i class="bi bi-shield-lock-fill fs-2 text-primary"></i>
            <div>
                <strong class="d-block text-dark fs-6">Tài khoản Quản trị viên tối cao (Hệ thống bảo vệ)</strong>
                <small class="text-secondary">Tài khoản này có quyền cao nhất trên toàn bộ hệ thống nên được bảo vệ nghiêm ngặt: không thể xóa, không thể khóa và không thể thay đổi vai trò.</small>
            </div>
        </div>
    @endif

    <p class="text-secondary">Các trường có dấu <span class="text-danger">*</span> là bắt buộc.</p>
    <form method="POST" action="{{ $isEdit ? route('users.update', $user) : route('users.store') }}">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <div class="row g-4">
            <div class="col-md-6">
                <label for="name" class="form-label">Họ và tên *</label>
                <input id="name" name="name" class="form-control" maxlength="255" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email *</label>
                <input id="email" name="email" type="email" class="form-control" maxlength="255" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">{{ $isEdit ? 'Mật khẩu mới' : 'Mật khẩu *' }}</label>
                <input id="password" name="password" type="password" class="form-control" minlength="6" @required(! $isEdit)>
                @if($isEdit)<div class="form-text">Để trống nếu không đổi mật khẩu.</div>@endif
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu{{ $isEdit ? '' : ' *' }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" minlength="6" @required(! $isEdit)>
            </div>
            <div class="col-md-4">
                <label for="role" class="form-label">Vai trò *</label>
                @if($isEdit && $user->isAdmin())
                    <input type="hidden" name="role" value="admin">
                    <select id="role" class="form-select bg-light" disabled style="cursor: not-allowed;">
                        <option value="admin" selected>Admin (Quản trị viên tối cao)</option>
                    </select>
                    <div class="form-text text-primary">
                        <i class="bi bi-shield-lock-fill me-1"></i>Vai trò Quản trị viên được bảo vệ cố định.
                    </div>
                @else
                    <select id="role" name="role" class="form-select" required>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                        <option value="teacher" @selected(old('role', $user->role) === 'teacher')>Giảng viên</option>
                        <option value="student" @selected(old('role', $user->role ?? 'student') === 'student')>Học viên</option>
                    </select>
                @endif
            </div>
            <div class="col-md-4">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input id="phone" name="phone" class="form-control" maxlength="20" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check mb-2">
                    @if($isEdit && $user->isAdmin())
                        <input type="hidden" name="is_active" value="1">
                        <input id="is_active" type="checkbox" value="1" class="form-check-input" checked disabled style="cursor: not-allowed;">
                        <label for="is_active" class="form-check-label text-success fw-semibold" style="cursor: not-allowed;" title="Tài khoản Quản trị viên luôn hoạt động để đảm bảo quyền quản trị hệ thống">
                            <i class="bi bi-shield-check me-1"></i>Tài khoản luôn hoạt động (Không thể khóa)
                        </label>
                    @else
                        <input id="is_active" name="is_active" type="checkbox" value="1" class="form-check-input" @checked(old('is_active', $isEdit ? $user->is_active : true))>
                        <label for="is_active" class="form-check-label">Tài khoản đang hoạt động</label>
                    @endif
                </div>
            </div>
        </div>
        <div class="border-top pt-4 mt-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit">{{ $isEdit ? 'Lưu thay đổi' : 'Thêm tài khoản' }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Hủy</a>
        </div>
    </form>
</div></div>

@endsection

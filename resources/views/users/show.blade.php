@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Chi tiết tài khoản')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <span class="badge bg-primary-subtle text-primary mb-2">#{{ $user->id }}</span>
        <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
        @switch($user->role)
            @case('admin')
                <span class="badge bg-danger">Admin</span>
                @break
            @case('teacher')
                <span class="badge bg-primary">Giảng viên</span>
                @break
            @default
                <span class="badge bg-success">Học viên</span>
        @endswitch
        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Hoạt động' : 'Bị khóa' }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Về danh sách</a>
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Sửa tài khoản</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Email</p><strong class="text-break">{{ $user->email }}</strong></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Số điện thoại</p><strong>{{ $user->phone ?? '—' }}</strong></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Ngày tạo</p><strong>{{ $user->created_at?->format('d/m/Y H:i') }}</strong></div></div></div>
</div>

@endsection

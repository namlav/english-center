@extends('layouts.app')

@section('title', 'Quản lý Tài khoản')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold mb-0"><i class="bi bi-people-fill"></i> Quản lý Tài khoản</h3>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm tài khoản
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td>
                                @switch($user->role)
                                    @case('admin')
                                        <span class="badge bg-danger">Admin</span>
                                        @break
                                    @case('teacher')
                                        <span class="badge bg-primary">Giáo viên</span>
                                        @break
                                    @default
                                        <span class="badge bg-success">Học viên</span>
                                @endswitch
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Bị khóa</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger ms-1" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Không có tài khoản nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
